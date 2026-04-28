<?php

namespace Database\Seeders;

use App\Models\GoogleGraduation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GoogleGraduationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            // Create a test user if none exist
            $users = User::factory(3)->create();
        }

        // Create graduation records for each user
        foreach ($users as $user) {
            // Check if already has graduation record
            if (!GoogleGraduation::where('user_id', $user->id)->exists()) {
                GoogleGraduation::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'letter_number' => 'SKL-' . date('Y') . '-' . str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'graduation_date' => now()->addMonths(random_int(1, 6))->format('Y-m-d'),
                ]);
            }
        }
    }
}

