<?php

namespace App\Console\Commands;

use App\Models\Artifact;
use App\Models\Collection;
use App\Models\Exhibition;
use App\Models\Museum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the XML sitemap for public SEO indexing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap.xml...');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Base URL
        $xml .= $this->createUrlNode(route('home'), '1.0', 'daily');
        $xml .= $this->createUrlNode(route('museums.index'), '0.8', 'daily');
        $xml .= $this->createUrlNode(route('artifacts.index'), '0.8', 'daily');
        $xml .= $this->createUrlNode(route('exhibitions.index'), '0.8', 'daily');

        // Museums
        $museums = Museum::where('verification_status', Museum::VERIFICATION_VERIFIED)->get();
        foreach ($museums as $museum) {
            $xml .= $this->createUrlNode(route('museums.show', $museum), '0.7', 'weekly');
        }

        // Artifacts
        $artifacts = Artifact::public()->get();
        foreach ($artifacts as $artifact) {
            $xml .= $this->createUrlNode(route('artifacts.show', $artifact), '0.7', 'weekly');
        }

        // Exhibitions
        $exhibitions = Exhibition::where('status', 'published')->get();
        foreach ($exhibitions as $exhibition) {
            $xml .= $this->createUrlNode(route('exhibitions.show', $exhibition), '0.6', 'monthly');
        }

        $xml .= '</urlset>';

        File::put(public_path('sitemap.xml'), $xml);

        $this->info('sitemap.xml has been generated successfully.');
    }

    private function createUrlNode($url, $priority = '0.5', $changefreq = 'weekly')
    {
        return '<url>' .
            '<loc>' . htmlspecialchars($url) . '</loc>' .
            '<lastmod>' . now()->tz('UTC')->toAtomString() . '</lastmod>' .
            '<changefreq>' . $changefreq . '</changefreq>' .
            '<priority>' . $priority . '</priority>' .
            '</url>';
    }
}
