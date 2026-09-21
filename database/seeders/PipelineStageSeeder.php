<?php

namespace Database\Seeders;

use App\Models\PipelineStage;
use Illuminate\Database\Seeder;

/**
 * A working pipeline out of the box. Every field here is editable from
 * Admin -> Pipeline; this is a starting point, not a fixed set.
 */
class PipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Intake',
                'slug' => 'intake',
                'description' => 'Uploaded and waiting to be picked up.',
                'client_message' => "We've got your files and they're queued with our production team.",
                'position' => 1,
                'color' => 'slate',
                'is_default' => true,
                'is_final' => false,
                'notifies_client' => true,
            ],
            [
                'name' => 'Preparing',
                'slug' => 'preparing',
                'description' => 'Transcoding, transcribing and sorting the raw material.',
                'client_message' => 'Your material is being prepared — transcribed, tidied and logged ready for the edit.',
                'position' => 2,
                'color' => 'blue',
                'is_default' => false,
                'is_final' => false,
                'notifies_client' => true,
            ],
            [
                'name' => 'Editing',
                'slug' => 'editing',
                'description' => 'The main cut.',
                'client_message' => 'Our editors are cutting your first draft.',
                'position' => 3,
                'color' => 'violet',
                'is_default' => false,
                'is_final' => false,
                'notifies_client' => true,
            ],
            [
                'name' => 'Internal review',
                'slug' => 'internal-review',
                'description' => 'Quality pass before anything reaches the client.',
                'client_message' => null,
                'position' => 4,
                'color' => 'amber',
                'is_default' => false,
                'is_final' => false,
                // Purely internal — the client doesn't need this ping.
                'notifies_client' => false,
            ],
            [
                'name' => 'Client review',
                'slug' => 'client-review',
                'description' => 'With the client for feedback.',
                'client_message' => 'Your draft is ready to watch — let us know what you would like changed.',
                'position' => 5,
                'color' => 'amber',
                'is_default' => false,
                'is_final' => false,
                'notifies_client' => true,
            ],
            [
                'name' => 'Delivered',
                'slug' => 'delivered',
                'description' => 'Final files handed over.',
                'client_message' => 'Your finished files are ready to download.',
                'position' => 6,
                'color' => 'emerald',
                'is_default' => false,
                'is_final' => true,
                'notifies_client' => true,
            ],
        ];

        foreach ($stages as $stage) {
            PipelineStage::updateOrCreate(['slug' => $stage['slug']], $stage);
        }
    }
}
