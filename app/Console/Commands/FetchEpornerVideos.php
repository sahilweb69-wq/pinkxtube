<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Performer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchEpornerVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options:
     *  --query=       Search query string
     *  --page=        Page to fetch (default: 1)
     *  --per-page=    Per page (default: 50, max per API limits)
     *  --order=       Order: latest, top, longest, etc. (per API)
     *  --period=      Rating period: day, week, month, year, all (if applicable)
     *  --max-pages=   Max pages to iterate (default: 1)
     */
    protected $signature = 'videos:fetch-eporner
        {--query= : Search query}
        {--page=1 : Page to fetch}
        {--per-page=50 : Items per page}
        {--order=latest : Order (e.g., latest, longest, shortest, top, top-weekly, top-monthly)}
        {--period=month : Period used only when order=top (e.g., day, week, month, year, all)}
        {--gay= : Include gay results (1 or 0). Defaults to task/config if provided}
        {--lq= : Low quality filter (1 or 0). Defaults to task/config if provided}
        {--format=json : Response format}
        {--thumbsize=big : Thumbnail size}
        {--max-pages=1 : Max pages to iterate}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch videos from Eporner API and store only new records (platform-agnostic schema).';

    protected string $baseUrl = 'https://www.eporner.com/api/v2/video/search/';

    public function handle(): int
    {
        $platform = 'eporner';

        // Resolve whether to run from CLI options or config-driven tasks
        $query = (string) $this->option('query');
        $page = (int) $this->option('page');
        $perPage = max(1, (int) $this->option('per-page'));
        $order = (string) $this->option('order');
        $period = (string) $this->option('period');
        $maxPages = max(1, (int) $this->option('max-pages'));

        $isDefaultCall = ($query === '' && $page === 1 && $perPage === 50 && $order === 'latest' && $period === 'month' && $maxPages === 1);

        $tasks = [];
        if ($isDefaultCall) {
            $cfg = config('video_fetch.eporner', []);
            $defaults = Arr::get($cfg, 'defaults', []);
            $tasks = Arr::get($cfg, 'tasks', []);
            if (empty($tasks)) {
                $tasks = [[
                    'query' => Arr::get($defaults, 'query'),
                    'order' => Arr::get($defaults, 'order', 'latest'),
                    'per_page' => Arr::get($defaults, 'per_page', 50),
                    'max_pages' => Arr::get($defaults, 'max_pages', 1),
                    'period' => Arr::get($defaults, 'period', 'month'),
                ]];
            }
        } else {
            $tasks = [[
                'query' => $query,
                'order' => $order,
                'per_page' => $perPage,
                'max_pages' => $maxPages,
                'period' => $period,
                'page' => $page,
            ]];
        }

        $totalNew = 0;
        foreach ($tasks as $task) {
            $q = (string) Arr::get($task, 'query', '');
            $ord = (string) Arr::get($task, 'order', $order);
            $pp = (int) Arr::get($task, 'per_page', $perPage);
            $mp = max(1, (int) Arr::get($task, 'max_pages', 1));
            $rt = (string) Arr::get($task, 'period', $period);
            $startPage = (int) Arr::get($task, 'page', 1);
            $sleepMs = (int) Arr::get($task, 'sleep_ms', (int) config('video_fetch.eporner.defaults.sleep_ms', 0));
            $gay = Arr::get($task, 'gay', $this->option('gay'));
            $lq = Arr::get($task, 'lq', $this->option('lq'));
            $format = (string) Arr::get($task, 'format', (string) $this->option('format') ?: 'json');
            $thumbsize = (string) Arr::get($task, 'thumbsize', (string) $this->option('thumbsize') ?: 'big');

            // Compose order string per Eporner style
            $orderParam = $ord;
            if ($ord === 'top' && !empty($rt)) {
                // Map period aliases to API style
                $map = [
                    'day' => 'top-daily',
                    'daily' => 'top-daily',
                    'week' => 'top-weekly',
                    'weekly' => 'top-weekly',
                    'month' => 'top-monthly',
                    'monthly' => 'top-monthly',
                    'year' => 'top-yearly',
                    'yearly' => 'top-yearly',
                    'all' => 'top-all',
                ];
                $orderParam = $map[strtolower($rt)] ?? 'top-weekly';
            }

            for ($p = $startPage; $p < $startPage + $mp; $p++) {
                $params = array_filter([
                    'query' => $q ?: null,
                    'page' => $p,
                    'per_page' => $pp,
                    'order' => $orderParam,
                    'thumbsize' => $thumbsize,
                    'gay' => is_null($gay) ? null : (int) $gay,
                    'lq' => is_null($lq) ? null : (int) $lq,
                    'format' => $format ?: 'json',
                ], fn ($v) => !is_null($v) && $v !== '');

                $this->info("Fetching page {$p} ...");
                try {
                    $response = $this->httpClient()
                        ->retry(3, 1000, function ($exception, $request) {
                            // Exponential backoff with jitter
                            usleep(random_int(100, 400) * 1000);
                            return true; // retry on all exceptions/status
                        })
                        ->get($this->baseUrl, $params);
                } catch (\Throwable $e) {
                    $this->error('HTTP request threw an exception: ' . $e->getMessage());
                    // Fallback: reduce page size once and retry quickly
                    if ($pp > 25) {
                        $pp = 25;
                        $this->line('Reducing per_page to 25 and retrying...');
                        $p--; // retry same page
                        if ($sleepMs > 0) usleep($sleepMs * 1000);
                        continue;
                    }
                    break;
                }

                if (!$response->ok()) {
                    $this->error('API request failed: ' . $response->status());
                    break;
                }

                $data = $response->json();
                $videos = Arr::get($data, 'videos', Arr::get($data, 'data', [])); // try common keys

                if (!is_array($videos) || empty($videos)) {
                    $this->warn('No videos returned for this page. Stopping.');
                    break;
                }

                // Normalize and collect unique IDs from payload
                $payload = collect($videos)->map(function ($item) use ($platform) {
                    // Eporner often uses keys: id, title, url, views, rating, rate, duration, embed, default_thumb, tags, pornstar, categories, added
                    $id = (string) Arr::get($item, 'id');
                    $title = (string) Arr::get($item, 'title');
                    $url = (string) Arr::get($item, 'url');
                    $views = (int) Arr::get($item, 'views', 0);
                    $rating = (float) (Arr::get($item, 'rate', Arr::get($item, 'rating')));
                    $likeCount = (int) Arr::get($item, 'likes', Arr::get($item, 'like_count', 0));
                    $dislikeCount = (int) Arr::get($item, 'dislikes', Arr::get($item, 'dislike_count', 0));
                    $duration = (int) Arr::get($item, 'length_sec', Arr::get($item, 'duration'));
                    $embed = (string) Arr::get($item, 'embed');
                    $defaultThumbData = Arr::get($item, 'default_thumb');
                    $defaultThumb = is_array($defaultThumbData) ? (string) Arr::get($defaultThumbData, 'src') : (string) ($defaultThumbData ?? Arr::get($item, 'thumb', ''));
                    $thumbWidth = is_array($defaultThumbData) ? (int) Arr::get($defaultThumbData, 'width') : null;
                    $thumbHeight = is_array($defaultThumbData) ? (int) Arr::get($defaultThumbData, 'height') : null;
                    $thumbs = Arr::get($item, 'thumbs', []);
                    $tags = Arr::get($item, 'tags', []);
                    $categories = Arr::get($item, 'categories', []);
                    $performers = Arr::get($item, 'pornstar', Arr::get($item, 'pornstars', []));
                    $added = Arr::get($item, 'added');
                    $keywordsStr = (string) Arr::get($item, 'keywords', '');
                    $keywordsArr = collect(explode(',', $keywordsStr))
                        ->map(fn($t) => trim($t))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    // Infer VR/3D from tags or title if not explicitly provided
                    $titleLower = Str::lower($title);
                    $allTags = collect(is_array($tags) ? $tags : (string) $tags)
                    ->merge($keywordsArr)
                    ->flatten()
                    ->map(fn($t) => Str::lower((string)$t))
                    ->all();
                    $isVr = (bool) (in_array('vr', $allTags, true) || Str::contains($titleLower, ' vr'));
                    $is3d = (bool) (in_array('3d', $allTags, true) || Str::contains($titleLower, ' 3d'));

                    return [
                        'platform' => $platform,
                        'source_id' => $id,
                        'title' => $title,
                        'source_url' => $url,
                        'views' => $views,
                        'like_count' => $likeCount,
                        'dislike_count' => $dislikeCount,
                        'rating' => $rating ?: null,
                        'duration' => $duration ?: null,
                        'embed_url' => $embed ?: null,
                        'thumb_url' => $defaultThumb ?: null,
                        'thumb_width' => $thumbWidth ?: null,
                        'thumb_height' => $thumbHeight ?: null,
                        'thumbs' => is_array($thumbs) ? array_values($thumbs) : null,
                        'keywords' => $keywordsStr ?: null,
                        'is_vr' => $isVr,
                        'is_3d' => $is3d,
                        'tags' => is_array($tags) ? array_values(array_unique(array_merge($tags, $keywordsArr))) : (string) $tags,
                        'categories' => is_array($categories) ? array_values($categories) : (string) $categories,
                        'performers' => is_array($performers) ? array_values($performers) : (string) $performers,
                        'uploaded_at' => $this->parseDate($added),
                        'raw' => $item,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

                $ids = $payload->pluck('source_id')->filter()->values()->all();

                if (empty($ids)) {
                    $this->warn('No valid IDs in payload. Stopping.');
                    break;
                }

                // Fetch existing IDs for this platform only
                $existingIds = Video::query()
                    ->where('platform', $platform)
                    ->whereIn('source_id', $ids)
                    ->pluck('source_id')
                    ->all();

                $toInsert = $payload->reject(function ($row) use ($existingIds) {
                    return in_array($row['source_id'], $existingIds, true);
                })->values();

                if ($toInsert->isEmpty()) {
                    $this->line('All videos on this page already exist.');
                    continue;
                }

                // Insert only new rows
                Video::query()->insert($toInsert->all());
                $count = $toInsert->count();
                $totalNew += $count;
                $this->info("Inserted {$count} new videos from page {$p}.");

                // Populate normalized pivot tables for newly inserted videos
                if ($count > 0) {
                    $newIds = $toInsert->pluck('source_id')->all();
                    $newVideos = Video::query()
                        ->where('platform', $platform)
                        ->whereIn('source_id', $newIds)
                        ->get(['id','tags','categories','performers','title']);

                    foreach ($newVideos as $video) {
                        $this->syncPivots($video);
                    }
                }

                // Optional: if API reports less than perPage, likely last page
                if (count($videos) < $pp) {
                    $this->line('Reached last page of results.');
                    break;
                }

                if ($sleepMs > 0) {
                    usleep($sleepMs * 1000);
                }
            }
        }

        $this->info("Done. Total new videos inserted: {$totalNew}.");
        return self::SUCCESS;
    }

    protected function parseDate($value): ?Carbon
    {
        if (!$value) return null;
        try {
            // Try a few common formats
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp((int) $value);
            }
            return Carbon::parse((string) $value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function syncPivots(Video $video): void
    {
        // Normalize arrays from JSON fields
        $tags = collect($video->tags ?? [])->filter()->unique()->values();
        $categories = collect($video->categories ?? [])->filter()->unique()->values();
        $performers = collect($video->performers ?? [])->filter()->unique()->values();

        $tagIds = [];
        foreach ($tags as $name) {
            $name = (string) $name;
            $slug = Str::slug($name);
            $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $name]);
            $tagIds[] = $tag->id;
        }
        if (!empty($tagIds)) {
            $video->tags()->syncWithoutDetaching($tagIds);
        }

        $categoryIds = [];
        foreach ($categories as $name) {
            $name = (string) $name;
            $slug = Str::slug($name);
            $cat = Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
            $categoryIds[] = $cat->id;
        }
        if (!empty($categoryIds)) {
            $video->categories()->syncWithoutDetaching($categoryIds);
        }

        $performerIds = [];
        foreach ($performers as $name) {
            $name = (string) $name;
            $slug = Str::slug($name);
            $perf = Performer::firstOrCreate(['slug' => $slug], ['name' => $name]);
            $performerIds[] = $perf->id;
        }
        if (!empty($performerIds)) {
            $video->performers()->syncWithoutDetaching($performerIds);
        }
    }

    protected function httpClient()
    {
        return Http::withHeaders([
                'User-Agent' => 'PinkxTubeBot/1.0 (+https://example.com) Laravel/' . app()->version(),
            ])
            ->acceptJson()
            ->withOptions([
                'timeout' => 20,          // seconds
                'connect_timeout' => 10,  // seconds
                'verify' => true,
                // Force HTTP/1.1 and IPv4 to avoid some cURL 28 cases
                'version' => 1.1,
                'allow_redirects' => true,
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ],
            ]);
    }
}
