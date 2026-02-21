<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'pdf'  => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt'  => 'text/plain',
        ];

        $ext = fake()->randomElement(array_keys($types));

        return [
            'user_id'       => User::factory(),
            'original_name' => fake()->word() . '.' . $ext,
            'mime_type'     => $types[$ext],
            'storage_path'  => 'private/documents/' . fake()->uuid() . '.' . $ext,
            'preview_path'  => null,
        ];
    }
}
