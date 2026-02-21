<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html as SpreadsheetHtmlWriter;

class PreviewService
{
    /**
     * Convert a document to HTML preview and store the result.
     *
     * Uses phpoffice libraries — pure PHP, zero external dependencies.
     */
    public function generatePreview(Document $document): bool
    {
        if (! $document->requiresConversion()) {
            return false;
        }

        $disk = Storage::disk('local');
        $sourcePath = $disk->path($document->storage_path);
        $previewDir = config('documents.preview_path');

        // Ensure preview directory exists
        if (! $disk->exists($previewDir)) {
            $disk->makeDirectory($previewDir);
        }

        try {
            $html = match ($document->extension) {
                'docx' => $this->convertDocxToHtml($sourcePath),
                'xlsx' => $this->convertXlsxToHtml($sourcePath),
                'pptx' => $this->convertPptxToHtml($sourcePath),
                default => null,
            };

            if ($html === null) {
                return false;
            }

            // Wrap in a styled shell
            $fullHtml = $this->wrapHtml($html, $document->extension);

            $previewRelativePath = $previewDir . '/' . $document->id . '.html';
            $disk->put($previewRelativePath, $fullHtml);

            $document->update(['preview_path' => $previewRelativePath]);

            Log::info('Document preview generated successfully', ['document_id' => $document->id]);
            return true;

        } catch (\Exception $e) {
            Log::error('Document preview conversion failed', [
                'document_id' => $document->id,
                'exception'   => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Convert DOCX to HTML using PhpWord.
     */
    private function convertDocxToHtml(string $sourcePath): string
    {
        $phpWord = WordIOFactory::load($sourcePath, 'Word2007');

        $htmlWriter = WordIOFactory::createWriter($phpWord, 'HTML');

        ob_start();
        $htmlWriter->save('php://output');
        $html = ob_get_clean();

        // Extract just the <body> content for cleaner embedding
        if (preg_match('/<body[^>]*>(.*)<\/body>/si', $html, $matches)) {
            return $matches[1];
        }

        return $html;
    }

    /**
     * Convert XLSX to HTML using PhpSpreadsheet.
     */
    private function convertXlsxToHtml(string $sourcePath): string
    {
        $spreadsheet = SpreadsheetIOFactory::load($sourcePath);
        $writer = new SpreadsheetHtmlWriter($spreadsheet);
        $writer->setPreCalculateFormulas(true);

        ob_start();
        $writer->save('php://output');
        $html = ob_get_clean();

        // Extract body content
        if (preg_match('/<body[^>]*>(.*)<\/body>/si', $html, $matches)) {
            return $matches[1];
        }

        return $html;
    }

    /**
     * Convert PPTX to HTML using PhpPresentation.
     *
     * Extracts slide text/shapes as styled HTML cards.
     */
    private function convertPptxToHtml(string $sourcePath): string
    {
        $presentation = \PhpOffice\PhpPresentation\IOFactory::load($sourcePath);
        $html = '';

        foreach ($presentation->getAllSlides() as $index => $slide) {
            $slideNum = $index + 1;
            $html .= '<div class="pptx-slide">';
            $html .= '<div class="pptx-slide-num">Slide ' . $slideNum . '</div>';

            foreach ($slide->getShapeCollection() as $shape) {
                if ($shape instanceof \PhpOffice\PhpPresentation\Shape\RichText) {
                    foreach ($shape->getParagraphs() as $paragraph) {
                        $text = '';
                        foreach ($paragraph->getRichTextElements() as $element) {
                            $t = htmlspecialchars($element->getText(), ENT_QUOTES, 'UTF-8');
                            if ($element instanceof \PhpOffice\PhpPresentation\Shape\RichText\Run) {
                                $font = $element->getFont();
                                $style = '';
                                if ($font->isBold()) $style .= 'font-weight:bold;';
                                if ($font->isItalic()) $style .= 'font-style:italic;';
                                if ($font->getSize()) $style .= 'font-size:' . $font->getSize() . 'pt;';
                                $text .= $style ? '<span style="' . $style . '">' . $t . '</span>' : $t;
                            } else {
                                $text .= $t;
                            }
                        }
                        if (trim($text)) {
                            $html .= '<p>' . $text . '</p>';
                        }
                    }
                } elseif ($shape instanceof \PhpOffice\PhpPresentation\Shape\Table) {
                    $html .= '<table class="pptx-table">';
                    foreach ($shape->getRows() as $row) {
                        $html .= '<tr>';
                        foreach ($row->getCells() as $cell) {
                            $cellText = '';
                            foreach ($cell->getParagraphs() as $paragraph) {
                                foreach ($paragraph->getRichTextElements() as $element) {
                                    $cellText .= htmlspecialchars($element->getText(), ENT_QUOTES, 'UTF-8');
                                }
                            }
                            $html .= '<td>' . $cellText . '</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '</table>';
                }
            }

            $html .= '</div>';
        }

        return $html ?: '<p style="color:#888;text-align:center;">No readable content found in this presentation.</p>';
    }

    /**
     * Wrap extracted HTML in a styled document shell.
     */
    private function wrapHtml(string $body, string $extension): string
    {
        $typeStyles = match ($extension) {
            'xlsx' => 'table { border-collapse: collapse; width: 100%; margin: 1rem 0; }
                       td, th { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: left; font-size: 13px; }
                       th { background: #f8fafc; font-weight: 600; }
                       tr:nth-child(even) { background: #f8fafc; }',
            'pptx' => '.pptx-slide { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2rem; margin: 1.5rem 0; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
                       .pptx-slide-num { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #f1f5f9; }
                       .pptx-table { border-collapse: collapse; width: 100%; margin: 1rem 0; }
                       .pptx-table td { border: 1px solid #e2e8f0; padding: 6px 10px; font-size: 13px; }',
            default => '',
        };

        return <<<HTML
                <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    color: #1e293b; line-height: 1.7; padding: 2rem; max-width: 900px; margin: 0 auto;
                    background: #fff; font-size: 14px;
                    -webkit-user-select: none; user-select: none;
                }
                h1 { font-size: 1.6rem; margin: 1.5rem 0 0.75rem; color: #0f172a; }
                h2 { font-size: 1.3rem; margin: 1.2rem 0 0.5rem; color: #1e293b; }
                h3 { font-size: 1.1rem; margin: 1rem 0 0.4rem; color: #334155; }
                p { margin: 0.5rem 0; }
                img { max-width: 100%; height: auto; }
                {$typeStyles}
                </style>
                </head>
                <body oncontextmenu="return false" onselectstart="return false" ondragstart="return false">
                {$body}
                </body>
                </html>
                HTML;
    }

    /**
     * Get the preview response for a document.
     */
    public function getPreviewResponse(Document $document): Response
    {
        $disk = Storage::disk('local');

        // PDF → stream inline directly
        if ($document->isPdf()) {
            return $this->streamInline($disk, $document->storage_path, 'application/pdf', $document->original_name);
        }

        // TXT → return safe text content
        if ($document->isText()) {
            return $this->streamInline($disk, $document->storage_path, 'text/plain', $document->original_name);
        }

        // Convertible types → stream the HTML preview
        if ($document->hasPreview()) {
            return $this->streamInline($disk, $document->preview_path, 'text/html', $document->original_name);
        }

        // Preview not available — try to generate it now
        if ($this->generatePreview($document)) {
            return $this->streamInline($disk, $document->preview_path, 'text/html', $document->original_name);
        }

        abort(500, 'Unable to generate document preview.');
    }

    /**
     * Stream a file inline from storage.
     */
    private function streamInline(
        \Illuminate\Contracts\Filesystem\Filesystem $disk,
        string $path,
        string $contentType,
        string $filename
    ): Response {
        abort_unless($disk->exists($path), 404, 'File not found.');

        $content = $disk->get($path);
        $size = strlen($content);

        return response($content, 200, [
            'Content-Type'           => $contentType,
            'Content-Length'         => $size,
            'Content-Disposition'    => 'inline; filename="' . $filename . '"',
            'Cache-Control'          => 'no-store, no-cache, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options'        => 'SAMEORIGIN',
            'Accept-Ranges'          => 'bytes',
        ]);
    }

    /**
     * Read text file content safely for rendering in <pre> tag.
     */
    public function getTextContent(Document $document): string
    {
        $disk = Storage::disk('local');

        abort_unless($disk->exists($document->storage_path), 404, 'File not found.');

        return $disk->get($document->storage_path);
    }
}
