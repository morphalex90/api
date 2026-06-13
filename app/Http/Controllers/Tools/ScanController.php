<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Tools\Scan;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleXMLElement;

final class ScanController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'url' => 'required|url',
            'auth_username' => 'nullable|string',
            'auth_password' => 'nullable|string',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 422);
        }

        $page = $this->fetchPage(
            $request->get('url'),
            $request->get('auth_username'),
            $request->get('auth_password'),
        );

        if ($page === false) {
            return response()->json(['message' => 'Page is not reachable'], 404);
        }

        $uuid = (string) Str::uuid();

        $scan = Scan::create([
            'url' => $request->get('url'),
            'uuid' => $uuid,
            'ip_address' => $request->ip(),
        ]);

        // Prime the cache so the first step does not refetch the page.
        Cache::put($this->cacheKey($uuid), $page, now()->addMinutes(10));

        return response()->json(['uuid' => $uuid], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $scan = Scan::where('uuid', $uuid)->select('url', 'created_at')->first();

        if ($scan !== null) {
            return response()->json(['scan' => $scan], 200);
        }

        return response()->json(['message' => 'Scan not found'], 404);

    }

    public function stepLinks($scan_uuid)
    {
        $info = $this->getPage($scan_uuid);
        $output = [];
        $count = 0;

        if ($info !== false) {

            $links = $info['dom']->getElementsByTagName('a');

            foreach ($links as $link) {

                $class = $title = '';

                $href = $link->getAttribute('href');
                $rel = $link->getAttribute('rel');

                // add base url in case the link does not have it
                if (mb_strpos($href, $info['base_url']) === false) { // base url non found
                    if (! filter_var($href, FILTER_VALIDATE_URL)) { // is not an url
                        $href = $info['base_url'] . $href;
                    }
                }

                // internal text
                $internalText = '';
                $titolo = $link->getAttribute('title');

                if (mb_trim($link->nodeValue) === '') { // if there is no text inside, search for images or other (using trim function because there might be spaces or tabs)

                    $img['title'] = '';
                    $img['src'] = '';

                    if ($link->childNodes->length > 1) { // loop inside the child only if there is content
                        foreach ($link->childNodes as $child) {
                            // echo '<pre>'.print_r($child,1).'</pre>';
                            if ($child->nodeName === 'img') { // get the src only for the images
                                $img['src'] = $child->getAttribute('src');
                                break;
                            }
                        }
                    }

                    if ($img['src'] !== '') {
                        $image_path = (mb_strpos($img['src'], $info['base_url']) !== false ? $img['src'] : $info['base_url'] . $img['src']); // add base url in case the image does not have it
                        $internalText = '<a href="' . $image_path . '" target="_blank" title="Open image"><img src="' . $image_path . '" style="max-width:200px;"></a>';
                    }
                } else { // the internal text is text
                    $internalText = $link->nodeValue;
                }

                if ($titolo === '') {
                    $class = 'notitle';
                    $title = 'Missing title tag';
                }

                $output[] = [
                    'error_class' => $class,
                    'title' => $title,
                    'title_attribute' => $titolo,
                    'href' => $href,
                    'internal_text' => $internalText,
                    'target' => $link->getAttribute('target'),
                    'rel' => $rel,
                    'class' => $link->getAttribute('class'),
                    'id' => $link->getAttribute('id'),

                ];
            }

            $count = $links->length;
        }

        return response()->json(['count' => $count, 'response' => $output]);
    }

    public function stepImages($scan_uuid)
    {
        $info = $this->getPage($scan_uuid);
        $output = [];
        $count = 0;

        if ($info !== false) {
            $imgs = $info['dom']->getElementsByTagName('img');

            foreach ($imgs as $img) {
                $class = $titolo = $href = '';
                if ($img->getAttribute('alt') === '') {
                    $class = 'noalt';
                    $titolo = 'Missing alt tag';
                }
                $href = $img->getAttribute('src');

                $output[] = [
                    'error_class' => $class,
                    'error_title' => $titolo,
                    'image' => (mb_stripos($href, $info['base_url']) !== false ? $href : $info['base_url'] . $href),
                    'data_scr' => $img->getAttribute('data-src'),
                    'alt' => $img->getAttribute('alt'),
                    'title' => $img->getAttribute('title'),
                    'height' => $img->getAttribute('height'),
                    'width' => $img->getAttribute('width'),
                    'class' => $img->getAttribute('class'),
                    'id' => $img->getAttribute('id'),
                ];
            }

            $count = $imgs->length;
        }

        return response()->json(['count' => $count, 'response' => $output]);
    }

    public function stepHeadings($scan_uuid)
    {
        $info = $this->getPage($scan_uuid);
        $output = [];
        $count_headings = 0;

        if ($info !== false) {

            $headings = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

            foreach ($headings as $heading) {
                $temps = $info['dom']->getElementsByTagName($heading);
                foreach ($temps as $temp) {
                    $output[] = [
                        'type' => mb_strtoupper($heading),
                        'text' => $temp->nodeValue,
                        'class' => $temp->getAttribute('class'),
                        'id' => $temp->getAttribute('id'),
                    ];
                    $count_headings++;
                }
            }
        }

        return response()->json(['count' => $count_headings, 'response' => $output]);
    }

    public function stepMeta($scan_uuid)
    {
        $info = $this->getPage($scan_uuid);
        $output = [];
        $count_meta = 0;

        if ($info !== false) {

            $metas = $info['dom']->getElementsByTagName('meta');
            foreach ($metas as $meta) {

                $record = [];

                if ($meta->getAttribute('property') !== '') {
                    $record['property'] = $meta->getAttribute('property');
                    $count_meta++;
                }

                if ($meta->getAttribute('name') !== '') {
                    $record['name'] = $meta->getAttribute('name');
                    $count_meta++;
                }

                if ($meta->getAttribute('itemprop') !== '') {
                    $record['itemprop'] = $meta->getAttribute('itemprop');
                    $count_meta++;
                }

                if ($meta->getAttribute('http-equiv') !== '') {
                    $record['http-equiv'] = $meta->getAttribute('http-equiv');
                    $count_meta++;
                }

                if ($meta->getAttribute('charset') !== '') {
                    $record['charset'] = $meta->getAttribute('charset');
                    $count_meta++;
                }

                if ($meta->getAttribute('charset') === '') {

                    $overridden = 0;

                    // themecolor, use the color for showing the span
                    if ($meta->getAttribute('name') === 'theme-color') {
                        $record['content'] = [
                            'color' => $meta->getAttribute('content'),
                            'content' => $meta->getAttribute('content'),
                        ];
                        $overridden = 1;
                    }

                    // check if og:url is correct
                    if ($meta->getAttribute('property') === 'og:url') {
                        $record['content'] = [
                            'color' => ($meta->getAttribute('content') === $info['url'] || $meta->getAttribute('content') === $info['base_url'] . '/' ? 'green' : 'red'),
                            'content' => $meta->getAttribute('content'),
                        ];
                        $overridden = 1;
                    }

                    // check if twitter:url is correct
                    if ($meta->getAttribute('name') === 'twitter:url') {
                        $record['content'] = [
                            'color' => ($meta->getAttribute('content') === $info['url'] || $meta->getAttribute('content') === $info['base_url'] . '/' ? 'green' : 'red'),
                            'content' => $meta->getAttribute('content'),
                        ];

                        $overridden = 1;
                    }

                    // check if og:image / twitter:image is correct
                    if ($meta->getAttribute('property') === 'og:image' || $meta->getAttribute('name') === 'twitter:image') {
                        $record['content'] = [
                            'color' => (@getimagesize($meta->getAttribute('content')) ? 'green' : 'red'),
                            'content' => $meta->getAttribute('content'),
                        ];

                        $overridden = 1;
                    }

                    // DEFAULT
                    if (! $overridden) {
                        $record['content'] = [
                            'color' => '',
                            'content' => $meta->getAttribute('content'),
                        ];
                    }

                    $count_meta++;
                }
                $output[] = $record;
            }
        }

        return response()->json(['count' => $count_meta, 'response' => $output]);
    }

    public function stepRobots($scan_uuid)
    {
        $info = $this->getPage($scan_uuid);
        $output = '';

        if ($info !== false) {

            $client = new \GuzzleHttp\Client(['http_errors' => false]);
            if ($info['auth'] === 1) {
                $response = $client->request('GET', $info['base_url'] . '/robots.txt', ['auth' => [$info['auth_username'], $info['auth_password']]]);
            } else {
                $response = $client->request('GET', $info['base_url'] . '/robots.txt', ['allow_redirects' => false]);
            }
            if ($response->getStatusCode() === 200) {
                $robots = $response->getBody()->getContents();
                $output .= ($robots !== '' ? '<pre>' . $robots . '</pre>' : 'Empty robots.txt');
            } else {
                $output .= 'Robots.txt not found (error ' . $response->getStatusCode() . ')';
            }
        }

        return response()->json(['response' => $output]);
    }

    public function stepSitemap($scan_uuid)
    {
        $info = $this->getPage($scan_uuid);
        $output = '';

        if ($info !== false) {

            $client = new \GuzzleHttp\Client(['http_errors' => false]);
            if ($info['auth'] === 1) {
                $response = $client->request('GET', $info['base_url'] . '/sitemap.xml', ['auth' => [$info['auth_username'], $info['auth_password']]]);
            } else {
                $response = $client->request('GET', $info['base_url'] . '/sitemap.xml', ['allow_redirects' => true]);
            }

            if ($response->getStatusCode() === 200) {
                $sitemap_response = $response->getBody()->getContents();
                $document = new DOMDocument;
                $document->loadXML($sitemap_response);
                $sitemap = $document->saveXML();

                if ($sitemap !== null && $sitemap !== '<!--?xml version="1.0"?-->') {
                    $xml = new SimpleXMLElement($sitemap);
                    $print = (htmlentities($xml->asXML()));
                    $output .= '<pre>' . str_replace('  ', '&nbsp;&nbsp;', $print) . '</pre>';
                } else {
                    $output .= 'Sitemap not found';
                }
            } else {
                $output .= 'sitemap.xml not found (error ' . $response->getStatusCode() . ')';
            }
        }

        return response()->json(['response' => $output]);
    }

    public function stepOthers($scan_uuid)
    {
        $info = $this->getPage($scan_uuid);
        $output = [];
        $count_others = 0;

        if ($info !== false) {

            $linksCanonical = $info['dom']->getElementsByTagName('link');

            foreach ($linksCanonical as $linkCanonical) {
                if ($linkCanonical->getAttribute('rel') === 'canonical') {
                    $canonical = (mb_strpos($linkCanonical->getAttribute('href'), $info['url']) !== false ? $linkCanonical->getAttribute('href') : $info['base_url'] . $linkCanonical->getAttribute('href'));

                    $output[] = [
                        'type' => 'Canonical',
                        'value' => $canonical,
                        'color' => ($canonical === $info['url'] || $canonical === $info['url'] . '/' ? 'green' : 'red'),
                    ];
                    $count_others++;
                }

                if ($linkCanonical->getAttribute('rel') === 'alternate' && $linkCanonical->getAttribute('hreflang') !== '') {
                    $output[] = [
                        'type' => 'Hreflang (' . $linkCanonical->getAttribute('hreflang') . ')',
                        'value' => $linkCanonical->getAttribute('href'),
                        'color' => '',
                    ];
                    $count_others++;
                }
            }
        }

        return response()->json(['count' => $count_others, 'response' => $output]);
    }

    public function stepStructuredData($scan_uuid)
    {
        $info = $this->getPage($scan_uuid);
        $output = [];
        $count_structured_data = 0;

        if ($info !== false) {

            // JSON-LD: <script type="application/ld+json">
            $scripts = $info['dom']->getElementsByTagName('script');
            foreach ($scripts as $script) {
                if (mb_strtolower($script->getAttribute('type')) !== 'application/ld+json') {
                    continue;
                }

                $raw = mb_trim($script->nodeValue);
                if ($raw === '') {
                    continue;
                }

                $decoded = json_decode($raw, true);
                $valid = json_last_error() === JSON_ERROR_NONE;

                $output[] = [
                    'type' => 'JSON-LD',
                    'schema' => $valid ? $this->extractSchemaTypes($decoded) : [],
                    'valid' => $valid,
                    'value' => $valid ? $decoded : $raw,
                ];
                $count_structured_data++;
            }

            // Microdata: elements declaring an itemscope with an itemtype
            $xpath = new DOMXPath($info['dom']);
            foreach ($xpath->query('//*[@itemscope][@itemtype]') as $node) {
                $properties = [];
                foreach ($xpath->query('.//*[@itemprop]', $node) as $prop) {
                    $properties[$prop->getAttribute('itemprop')] = $this->microdataValue($prop);
                }

                $output[] = [
                    'type' => 'Microdata',
                    'schema' => array_filter([$node->getAttribute('itemtype')]),
                    'valid' => true,
                    'value' => $properties,
                ];
                $count_structured_data++;
            }
        }

        return response()->json(['count' => $count_structured_data, 'response' => $output]);
    }

    /**
     * Pull the schema.org `@type`(s) out of a decoded JSON-LD payload,
     * accounting for `@graph` wrappers and lists of items.
     *
     * @return list<string>
     */
    private function extractSchemaTypes(mixed $decoded): array
    {
        if (! is_array($decoded)) {
            return [];
        }

        $items = $decoded['@graph'] ?? $decoded;

        if (! array_is_list($items)) {
            $items = [$items];
        }

        $types = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item['@type'])) {
                $types = array_merge($types, (array) $item['@type']);
            }
        }

        return array_values(array_unique($types));
    }

    /**
     * Resolve the value of a microdata `itemprop` element, following the
     * HTML microdata rules for where the value lives per tag.
     */
    private function microdataValue(DOMElement $node): string
    {
        return match (mb_strtolower($node->nodeName)) {
            'meta' => $node->getAttribute('content'),
            'img', 'audio', 'video', 'embed', 'iframe', 'source', 'track' => $node->getAttribute('src'),
            'a', 'area', 'link' => $node->getAttribute('href'),
            'object' => $node->getAttribute('data'),
            'data', 'meter' => $node->getAttribute('value'),
            'time' => $node->getAttribute('datetime') !== '' ? $node->getAttribute('datetime') : mb_trim($node->nodeValue),
            default => mb_trim($node->nodeValue),
        };
    }

    /**
     * Resolve the scan's page once and parse it into a DOM document.
     *
     * The fetched HTML is cached per scan so the multiple step endpoints do
     * not each trigger a fresh HTTP request to the target site.
     *
     * @return array{auth: int, auth_username: ?string, auth_password: ?string, base_url: string, dom: DOMDocument, url: string}|false
     */
    private function getPage(string $scan_uuid): array|false
    {
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();

        if ($scan === null) {
            return false;
        }

        $cacheKey = $this->cacheKey($scan_uuid);
        $page = Cache::get($cacheKey);

        if ($page === null) {
            $page = $this->fetchPage($scan->url);

            if ($page === false) {
                return false;
            }

            Cache::put($cacheKey, $page, now()->addMinutes(10));
        }

        $dom = new DOMDocument;
        @$dom->loadHTML(mb_encode_numericentity($page['html'], [0x80, 0x10FFFF, 0, ~0], 'UTF-8'));

        return [
            'auth' => $page['auth'],
            'auth_username' => $page['auth_username'],
            'auth_password' => $page['auth_password'],
            'base_url' => $page['base_url'],
            'dom' => $dom,
            'url' => $page['url'],
        ];
    }

    /**
     * Fetch the raw page HTML and metadata. Returns a cacheable array (no DOM,
     * which is not serializable) or false when the page is not reachable.
     *
     * @return array{auth: int, auth_username: ?string, auth_password: ?string, base_url: string, html: string, url: string}|false
     */
    private function fetchPage(string $url, ?string $authUsername = null, ?string $authPassword = null): array|false
    {
        $site = parse_url($url);

        if (! isset($site['scheme'], $site['host'])) {
            return false;
        }

        $base_url = $site['scheme'] . '://' . $site['host'];

        $client = new \GuzzleHttp\Client(['http_errors' => false]);

        if (filled($authUsername) && filled($authPassword)) {
            $response = $client->request('GET', $url, ['auth' => [$authUsername, $authPassword]]);
            $auth = 1;
        } else {
            $response = $client->request('GET', $url, ['allow_redirects' => false]);
            $auth = 0;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        return [
            'auth' => $auth,
            'auth_username' => $authUsername,
            'auth_password' => $authPassword,
            'base_url' => $base_url,
            'html' => $response->getBody()->getContents(),
            'url' => $url,
        ];
    }

    private function cacheKey(string $scan_uuid): string
    {
        return 'tools:scan:' . $scan_uuid . ':page';
    }
}
