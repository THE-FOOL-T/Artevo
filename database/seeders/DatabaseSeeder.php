<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with one predictable account per
     * role (fixed emails, password "password") plus a handful of random
     * collectors/visitors via the factory, so local development always
     * has something to log in as.
     */
    public function run(): void
    {
        $this->seedUser('admin', 'Artevo Admin', 'admin@artevo.test');
        $this->seedUser('curator', 'Nadia Farouk', 'curator@artevo.test');
        $this->seedUser('collector', 'Marcus Webb', 'collector@artevo.test');
        $this->seedUser('visitor', 'Visitor Account', 'visitor@artevo.test');

        User::factory()->count(2)->curator()->create();
        User::factory()->count(5)->collector()->create();
        User::factory()->count(5)->visitor()->create();
    }

    protected function seedUser(string $role, string $name, string $email): void
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->fill([
            'name' => $name,
            'email' => $email,
            'password' => $user->exists ? $user->password : bcrypt('password'),
            'email_verified_at' => $user->email_verified_at ?? now(),
            'role' => $role,
        ]);

        $user->save();
    }
}
