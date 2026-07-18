<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Wraps the Metropolitan Museum of Art's free public Collection API.
 * https://metmuseum.github.io/
 * No API key required.
 */
class MetMuseumService
{
    protected const BASE = 'https://collectionapi.metmuseum.org/public/collection/v1';

    /**
     * Search the Met collection by keyword and return up to 12 results
     * that all have usable thumbnail images AND are actually related to the query.
     *
     * Fixes applied vs v1:
     * - Search cache is NOT used — every search always hits the live API so the
     *   results are fresh and query-specific (avoids stale cache returning the
     *   same portrait for every search term).
     * - Slice is now 100 IDs instead of 20, so we have enough candidates when
     *   filtering for primaryImageSmall.
     * - We re-check relevance: each result's title / culture / objectName must
     *   contain at least one word from the query (case-insensitive), preventing
     *   completely unrelated objects from slipping through.
     * - Object-level cache is kept (24 h) to avoid re-fetching the same object.
     */
    public function search(string $query, int $limit = 12): array
    {
        // Hit the Met search API fresh every time — no search-level caching.
        $response = Http::timeout(15)->get(self::BASE . '/search', [
            'q'         => $query,
            'hasImages' => true,
        ]);

        if (! $response->successful()) {
            return [];
        }

        $objectIds = $response->json('objectIDs', []);

        if (empty($objectIds)) {
            return [];
        }

        // Build a set of query words for relevance filtering (words ≥ 3 chars)
        $queryWords = array_filter(
            array_map('strtolower', preg_split('/\s+/', trim($query))),
            fn($w) => strlen($w) >= 3
        );

        // Try up to the first 100 IDs to find $limit good results
        $slice   = array_slice($objectIds, 0, 100);
        $results = [];

        foreach ($slice as $id) {
            if (count($results) >= $limit) break;

            $detail = $this->getObject($id);

            // Must have a thumbnail
            if (! $detail || empty($detail['primaryImageSmall'])) {
                continue;
            }

            // Relevance check — at least one query word must appear in key fields
            if (! empty($queryWords) && ! $this->isRelevant($detail, $queryWords)) {
                continue;
            }

            $results[] = $this->normalise($detail);
        }

        return $results;
    }

    /**
     * Fetch a single object by its Met objectID (cached 24 h).
     */
    public function getObject(int $objectId): ?array
    {
        $cacheKey = 'met_object_' . $objectId;

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($objectId) {
            $response = Http::timeout(10)->get(self::BASE . '/objects/' . $objectId);

            if (! $response->successful()) {
                return null;
            }

            return $response->json();
        });
    }

    /**
     * Map a raw Met API object to the fields Artevo's artifact form expects.
     */
    public function normalise(array $obj): array
    {
        return [
            'met_id'            => $obj['objectID'] ?? null,
            'title'             => $obj['title'] ?? '',
            'description'       => $this->buildDescription($obj),
            'civilization'      => $obj['culture'] ?? '',
            'era'               => $obj['period'] ?? '',
            'country_of_origin' => $obj['country'] ?? '',
            'dimensions'        => $obj['dimensions'] ?? '',
            'medium'            => $obj['medium'] ?? '',
            'date_label'        => $obj['objectDate'] ?? '',
            'department'        => $obj['department'] ?? '',
            'artist'            => $obj['artistDisplayName'] ?? '',
            'credit_line'       => $obj['creditLine'] ?? '',
            'image_url'         => $obj['primaryImage'] ?? $obj['primaryImageSmall'] ?? '',
            'image_thumb'       => $obj['primaryImageSmall'] ?? '',
            'met_url'           => $obj['objectURL'] ?? '',
            'tags'              => collect($obj['tags'] ?? [])->pluck('term')->implode(', '),
        ];
    }

    /**
     * Returns true if at least one query word appears in any key field of the object.
     */
    private function isRelevant(array $obj, array $queryWords): bool
    {
        $haystack = strtolower(implode(' ', array_filter([
            $obj['title']             ?? '',
            $obj['objectName']        ?? '',
            $obj['culture']           ?? '',
            $obj['period']            ?? '',
            $obj['medium']            ?? '',
            $obj['department']        ?? '',
            $obj['artistDisplayName'] ?? '',
            $obj['country']           ?? '',
            implode(' ', array_column($obj['tags'] ?? [], 'term')),
        ])));

        foreach ($queryWords as $word) {
            if (str_contains($haystack, $word)) {
                return true;
            }
        }

        return false;
    }

    private function buildDescription(array $obj): string
    {
        $parts = [];

        if (! empty($obj['objectName']))        $parts[] = $obj['objectName'];
        if (! empty($obj['medium']))            $parts[] = 'Medium: ' . $obj['medium'];
        if (! empty($obj['objectDate']))        $parts[] = 'Date: ' . $obj['objectDate'];
        if (! empty($obj['culture']))           $parts[] = 'Culture: ' . $obj['culture'];
        if (! empty($obj['period']))            $parts[] = 'Period: ' . $obj['period'];
        if (! empty($obj['artistDisplayName'])) $parts[] = 'Artist: ' . $obj['artistDisplayName'];
        if (! empty($obj['creditLine']))        $parts[] = $obj['creditLine'];
        if (! empty($obj['repository']))        $parts[] = 'Repository: ' . $obj['repository'];

        return implode('. ', array_filter($parts)) . '.';
    }
}
