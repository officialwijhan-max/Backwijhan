<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seeds an admin and a manager account for local development.
     * Credentials come from the environment — never hard-coded — and
     * default to placeholders that must be changed before real use.
     */
    public function run(): void
    {
        // Never create accounts with the well-known placeholder password on
        // a production database.
        if (app()->isProduction() && (
            ! env('ADMIN_SEED_PASSWORD') || env('ADMIN_SEED_PASSWORD') === 'ChangeMe123!'
            || ! env('MANAGER_SEED_PASSWORD') || env('MANAGER_SEED_PASSWORD') === 'ChangeMe123!'
        )) {
            $this->command?->error('Refusing to seed admin users in production with default/empty passwords. Set ADMIN_SEED_PASSWORD and MANAGER_SEED_PASSWORD.');

            return;
        }

        User::updateOrCreate(
            ['email' => env('ADMIN_SEED_EMAIL', 'admin@wijhan.com')],
            [
                'name' => 'Wijhan Admin',
                'password' => env('ADMIN_SEED_PASSWORD', 'ChangeMe123!'),
                'role' => AdminRole::Admin,
                'is_active' => true,
            ],
        );

        User::updateOrCreate(
            ['email' => env('MANAGER_SEED_EMAIL', 'manager@wijhan.com')],
            [
                'name' => 'Wijhan Manager',
                'password' => env('MANAGER_SEED_PASSWORD', 'ChangeMe123!'),
                'role' => AdminRole::Manager,
                'is_active' => true,
            ],
        );
    }
}
