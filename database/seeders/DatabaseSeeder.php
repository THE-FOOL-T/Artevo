<?php

namespace Database\Seeders;

use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\ArtifactMaterial;
use App\Models\Museum;
use App\Models\MuseumContact;
use App\Models\MuseumImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        $admin = User::updateOrCreate(
            ['email' => 'admin@artevo.test'],
            [
                'name' => 'Artevo Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'avatar_path' => null,
                'role' => User::ROLE_ADMIN,
                'remember_token' => Str::random(10),
            ]
        );

        $curator = User::updateOrCreate(
            ['email' => 'curator@artevo.test'],
            [
                'name' => 'Nadia Farouk',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'avatar_path' => null,
                'role' => User::ROLE_CURATOR,
                'remember_token' => Str::random(10),
            ]
        );

        $collector = User::updateOrCreate(
            ['email' => 'collector@artevo.test'],
            [
                'name' => 'Marcus Webb',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'avatar_path' => null,
                'role' => User::ROLE_COLLECTOR,
                'remember_token' => Str::random(10),
            ]
        );

        $visitor = User::updateOrCreate(
            ['email' => 'visitor@artevo.test'],
            [
                'name' => 'Visitor Account',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'avatar_path' => null,
                'role' => User::ROLE_VISITOR,
                'remember_token' => Str::random(10),
            ]
        );

        $otherCurators = User::factory()->count(2)->curator()->create();
        User::factory()->count(5)->collector()->create();
        User::factory()->count(5)->visitor()->create();

        // A handful of sample museums, mostly owned by the fixed demo
        // curator so /curator/museums always has something to show.
        $demoMuseums = Museum::factory()
            ->count(3)
            ->featured()
            ->for($curator, 'curator')
            ->create()
            ->each(function (Museum $museum) {
                MuseumImage::factory()->count(3)->for($museum)->create();
                MuseumContact::factory()->for($museum)->create();
            });

        Museum::factory()
            ->count(2)
            ->recycle($otherCurators)
            ->create()
            ->each(function (Museum $museum) {
                MuseumImage::factory()->count(2)->for($museum)->create();
                MuseumContact::factory()->for($museum)->create();
            });
              // Artifact lookup tables — Phase 7. Factories pick randomly from
        // a fixed word list and dedupe on unique name/slug, so calling
        // these a fixed number of times reliably seeds "one of each."
        $categories = ArtifactCategory::factory()->count(10)->create();
        $materials = ArtifactMaterial::factory()->count(12)->create();

        // Sample artifacts for the demo curator's first museum...
        Artifact::factory()
            ->count(6)
            ->forMuseum($demoMuseums->first())
            ->recycle($categories)
            ->recycle($materials)
            ->create();

        // ...and for the demo collector's personal collection.
        Artifact::factory()
            ->count(4)
            ->recycle($categories)
            ->recycle($materials)
            ->create([
                'collector_id' => $collector->id,
                'created_by' => $collector->id,
            ]);
    }
}
