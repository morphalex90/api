<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Tools\Scan;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleXMLElement;

class ScanController extends Controller
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
            return response()->json(['message' => $validator->errors()->all()], 403);
        }

        $requestUrl = new Request($request->all());
        $response = $this->checkUrl($requestUrl);
        if ($response == false) {
            return response()->json(['message' => 'Page is not reachable'], 404);
        }

        $scan = Scan::create([
            'url' => $request->get('url'),
            'uuid' => Str::uuid(),
            'ip_address' => $request->ip(),
        ]);

        // Mail to myself
        // $headers = "MIME-Version: 1.0" . "\r\n";
        // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // $headers .= 'From: Info <info@morpheus90.com>' . "\r\n";
        // $content = 'Website: ' . $request->get('url') . '<br>';
        // mail('piero.nanni@gmail.com', 'Tools By Piero Nanni - New Search', $content, $headers);

        return response()->json(['uuid' => $scan->uuid], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $scan = Scan::where('uuid', $uuid)->select('url', 'created_at')->first();

        if ($scan != null) {
            return response()->json(['scan' => $scan], 200);
        } else {
            return response()->json(['message' => 'Scan not found'], 404);
        }
    }

    private function checkUrl(Request $request)
    {
        $url = $request->get('url');
        $auth_username = $request->get('auth_username');
        $auth_password = $request->get('auth_password');

        $site = parse_url($url); // get the array of the url
        $base_url = $site['scheme'] . '://' . $site['host']; // build the base_url for later

        $auth = 0;
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        if ($auth_username != '' && $auth_password != '') {
            $response = $client->request('GET', $url, ['auth' => [$auth_username, $auth_password]]);
            $auth = 1;
        } else {
            $response = $client->request('GET', $url, ['allow_redirects' => false]);
        }

        if ($response->getStatusCode() == 200) { // only if the url is correct

            $dom = new DOMDocument;
            @$dom->loadHTML(mb_convert_encoding($response->getBody()->getContents(), 'HTML-ENTITIES', 'UTF-8'));

            return [
                'auth' => $auth,
                'base_url' => $base_url,
                'dom' => $dom,
                'url' => $url,
            ];
        } else {
            return false;
        }
    }

    public function stepLinks($scan_uuid)
    {
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();
        $info = $this->checkUrl(new Request(['url' => $scan->url]));

        if ($info != false) {

            $links = $info['dom']->getElementsByTagName('a');
            $output = [];

            foreach ($links as $link) {

                $class = $title = '';

                $href = $link->getAttribute('href');
                $rel = $link->getAttribute('rel');

                // add base url in case the link does not have it
                if (strpos($href, $info['base_url']) === false) { // base url non found
                    if (!filter_var($href, FILTER_VALIDATE_URL)) { // is not an url
                        $href = $info['base_url'] . $href;
                    }
                }

                // internal text
                $internalText = '';
                $titolo = $link->getAttribute('title');

                if (trim($link->nodeValue) == '') { // if there is no text inside, search for images or other (using trim function because there might be spaces or tabs)

                    $img['title'] = '';
                    $img['src'] = '';

                    if ($link->childNodes->length > 1) { // loop inside the child only if there is content
                        foreach ($link->childNodes as $child) {
                            // echo '<pre>'.print_r($child,1).'</pre>';
                            if ($child->nodeName == 'img') { // get the src only for the images
                                $img['src'] = $child->getAttribute('src');
                                break;
                            }
                        }
                    }

                    if ($img['src'] != '') {
                        $image_path = (strpos($img['src'], $info['base_url']) !== false ? $img['src'] : $info['base_url'] . $img['src']); // add base url in case the image does not have it
                        $internalText = '<a href="' . $image_path . '" target="_blank" title="Open image"><img src="' . $image_path . '" style="max-width:200px;"></a>';
                    }
                } else { // the internal text is text
                    $internalText = $link->nodeValue;
                }

                if ($titolo == '') {
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
        }

        return response()->json(['count' => count($links), 'response' => $output]);
    }

    public function stepImages($scan_uuid)
    {
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();
        $info = $this->checkUrl(new Request(['url' => $scan->url]));
        $output = [];

        if ($info != false) {
            $imgs = $info['dom']->getElementsByTagName('img');

            foreach ($imgs as $img) {
                $class = $titolo = $href = '';
                if ($img->getAttribute('alt') == '') {
                    $class = 'noalt';
                    $titolo = 'Missing alt tag';
                }
                $href = $img->getAttribute('src');

                $output[] = [
                    'error_class' => $class,
                    'error_title' => $titolo,
                    'image' => (stripos($href, $info['base_url']) !== false ? $href : $info['base_url'] . $href),
                    'data_scr' => $img->getAttribute('data-src'),
                    'alt' => $img->getAttribute('alt'),
                    'title' => $img->getAttribute('title'),
                    'height' => $img->getAttribute('height'),
                    'width' => $img->getAttribute('width'),
                    'class' => $img->getAttribute('class'),
                    'id' => $img->getAttribute('id'),
                ];

                // $output .= '<tr class="' . $class . '" title="' . $titolo . '"><td><a href="' . (stripos($href, $info['base_url']) !== false ? $href : $info['base_url'] . $href) . '" target="_blank"><img src="' . (stripos($href, $info['base_url']) !== false ? $href : $info['base_url'] . $href) . '" style="max-width:300px;"></a></td><td>' . $img->getAttribute('data-src') . '</td><td>' . $img->getAttribute('alt') . '</td><td>' . $img->getAttribute('title') . '</td><td>' . $img->getAttribute('height') . '</td><td>' . $img->getAttribute('width') . '</td><td>' . $img->getAttribute('class') . '</td><td>' . $img->getAttribute('id') . '</td></tr>';

            }
        }

        return response()->json(['count' => count($imgs), 'response' => $output]);
    }

    public function stepHeadings($scan_uuid)
    {
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();
        $info = $this->checkUrl(new Request(['url' => $scan->url]));
        $output = [];

        if ($info != false) {

            $count_headings = 0;
            $headings = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

            foreach ($headings as $heading) {
                $temps = $info['dom']->getElementsByTagName($heading);
                foreach ($temps as $temp) {
                    $output[] = [
                        'type' => strtoupper($heading),
                        'text' => $temp->nodeValue,
                        'class' => $temp->getAttribute('class'),
                        'id' => $temp->getAttribute('id'),
                    ];
                    // $output .= '<tr><td>' . strtoupper($heading) . '</td><td>' . $temp->nodeValue . '</td><td>' . $temp->getAttribute('class') . '</td><td>' . $temp->getAttribute('id') . '</td></tr>';
                    $count_headings++;
                }
            }
        }

        return response()->json(['count' => $count_headings, 'response' => $output]);
    }

    public function stepMeta($scan_uuid)
    {
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();
        $info = $this->checkUrl(new Request(['url' => $scan->url]));
        $output = [];

        if ($info != false) {

            $metas = $info['dom']->getElementsByTagName('meta');
            $count_meta = 0;
            // $output .= '<tr><th>Type</th><th>Label</th><th>Content</th></tr>';
            $output = [];
            foreach ($metas as $meta) {

                $record = [];

                if ($meta->getAttribute('property') != '') {
                    $record['property'] = $meta->getAttribute('property');
                    $count_meta++;
                }

                if ($meta->getAttribute('name') != '') {
                    $record['name'] = $meta->getAttribute('name');
                    $count_meta++;
                }

                if ($meta->getAttribute('itemprop') != '') {
                    $record['itemprop'] = $meta->getAttribute('itemprop');
                    $count_meta++;
                }

                if ($meta->getAttribute('http-equiv') != '') {
                    $record['http-equiv'] = $meta->getAttribute('http-equiv');
                    $count_meta++;
                }

                if ($meta->getAttribute('charset') != '') {
                    $record['charset'] = $meta->getAttribute('charset');
                    $count_meta++;
                }

                if ($meta->getAttribute('charset') == '') {

                    $overridden = 0;

                    // themecolor, use the color for showing the span
                    if ($meta->getAttribute('name') == 'theme-color') {
                        $record['content'] = [
                            'color' => $meta->getAttribute('content'),
                            'content' => $meta->getAttribute('content'),
                        ];
                        // $output .= '<td><span style="color:' . $meta->getAttribute('content') . ';">' . $meta->getAttribute('content') . '</span></td></tr>';
                        $overridden = 1;
                    }

                    // check if og:url is correct
                    if ($meta->getAttribute('property') == 'og:url') {
                        // if ($meta->getAttribute('content') == $info['url'] || $meta->getAttribute('content') == $info['url'] . '/') {
                        //     $output .= '<td><span style="color:green;">' . $meta->getAttribute('content') . '</span></td></tr>';
                        // } else {
                        //     $output .= '<td><span style="color:red;">' . $meta->getAttribute('content') . '</span></td></tr>';
                        // }

                        $record['content'] = [
                            'color' => ($meta->getAttribute('content') == $info['url'] || $meta->getAttribute('content') == $info['base_url'] . '/' ? 'green' : 'red'),
                            'content' => $meta->getAttribute('content'),
                        ];
                        $overridden = 1;
                    }

                    // check if twitter:url is correct
                    if ($meta->getAttribute('name') == 'twitter:url') {
                        // if ($meta->getAttribute('content') == $info['url'] || $meta->getAttribute('content') == $info['url'] . '/') {
                        //     $output .= '<td><span style="color:green;">' . $meta->getAttribute('content') . '</span></td></tr>';
                        // } else {
                        //     $output .= '<td><span style="color:red;">' . $meta->getAttribute('content') . '</span></td></tr>';
                        // }

                        $record['content'] = [
                            'color' => ($meta->getAttribute('content') == $info['url'] || $meta->getAttribute('content') == $info['base_url'] . '/' ? 'green' : 'red'),
                            'content' => $meta->getAttribute('content'),
                        ];

                        $overridden = 1;
                    }

                    // check if og:image / twitter:image is correct
                    if ($meta->getAttribute('property') == 'og:image' || $meta->getAttribute('name') == 'twitter:image') {
                        // if (@getimagesize($meta->getAttribute('content'))) {
                        //     $output .= '<td><span style="color:green;">' . $meta->getAttribute('content') . '</span></td></tr>';
                        // } else {
                        //     $output .= '<td><span style="color:red;">' . $meta->getAttribute('content') . '</span></td></tr>';
                        // }

                        $record['content'] = [
                            'color' => (@getimagesize($meta->getAttribute('content')) ? 'green' : 'red'),
                            'content' => $meta->getAttribute('content'),
                        ];

                        $overridden = 1;
                    }

                    // DEFAULT
                    if (!$overridden) {
                        $record['content'] = [
                            'color' => '',
                            'content' => $meta->getAttribute('content'),
                        ];
                        // $output .= '<td>' . $meta->getAttribute('content') . '</td></tr>';
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
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();
        $info = $this->checkUrl(new Request(['url' => $scan->url]));
        $output = '';

        if ($info != false) {

            $client = new \GuzzleHttp\Client(['http_errors' => false]);
            if ($info['auth'] == 1) {
                $response = $client->request('GET', $info['base_url'] . '/robots.txt', ['auth' => [$info['auth_username'], $info['auth_password']]]);
            } else {
                $response = $client->request('GET', $info['base_url'] . '/robots.txt', ['allow_redirects' => false]);
            }
            if ($response->getStatusCode() == 200) {
                $robots = $response->getBody()->getContents();
                $output .= ($robots != null ? '<pre>' . ($robots) . '</pre>' : 'Empty robots.txt');

                if ($robots == null) {
                    $$output .= 'Empty robots.txt';
                }
            } else {
                $$output .= 'Robots.txt not found (error ' . $response->getStatusCode() . ')';
            }
        }

        return response()->json(['response' => $output]);
    }

    public function stepSitemap($scan_uuid)
    {
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();
        $info = $this->checkUrl(new Request(['url' => $scan->url]));
        $output = '';

        if ($info != false) {

            $client = new \GuzzleHttp\Client(['http_errors' => false]);
            if ($info['auth'] == 1) {
                $response = $client->request('GET', $info['base_url'] . '/sitemap.xml', ['auth' => [$info['auth_username'], $info['auth_password']]]);
            } else {
                $response = $client->request('GET', $info['base_url'] . '/sitemap.xml', ['allow_redirects' => true]);
            }

            if ($response->getStatusCode() == 200) {
                $sitemap_response = $response->getBody()->getContents();
                $document = new DOMDocument;
                $document->loadXML($sitemap_response);
                $sitemap = $document->saveXML();

                if ($sitemap != null && $sitemap != '<!--?xml version="1.0"?-->') {
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
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();
        $info = $this->checkUrl(new Request(['url' => $scan->url]));
        $output = [];

        if ($info != false) {

            $count_others = 0;
            $linksCanonical = $info['dom']->getElementsByTagName('link');

            // $output .= '<tr><th>Type</th><th>Value</th></tr>';
            foreach ($linksCanonical as $linkCanonical) {
                if ($linkCanonical->getAttribute('rel') == 'canonical') {
                    $canonical = (strpos($linkCanonical->getAttribute('href'), $info['url']) !== false ? $linkCanonical->getAttribute('href') : $info['base_url'] . $linkCanonical->getAttribute('href'));

                    // if ($canonical == $info['url'] || $canonical == $info['url'] . '/') {
                    //     $output .= '<tr><td>Canonical</td><td><span style="color:green;">' . $canonical . '</span></td></tr>';
                    // } else {
                    //     $output .= '<tr><td>Canonical</td><td><span style="color:red;">' . $canonical . '</span></td></tr>';
                    // }

                    $output[] = [
                        'type' => 'Canonical',
                        'value' => $canonical,
                        'color' => ($canonical == $info['url'] || $canonical == $info['url'] . '/' ? 'green' : 'red'),
                    ];
                    $count_others++;
                }

                if ($linkCanonical->getAttribute('rel') == 'alternate' && $linkCanonical->getAttribute('hreflang') != '') {
                    // $output .= '<tr><td>Hreflang (' . $linkCanonical->getAttribute('hreflang') . ')</td><td>' . $linkCanonical->getAttribute('href') . '</td></tr>';
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
        $scan = Scan::where('uuid', $scan_uuid)->select('url')->first();
        $info = $this->checkUrl(new Request(['url' => $scan->url]));
        $output = '';

        if ($info != false) {

            $count_structured_data = 0;

            $output .= 'WORK IN PROGRESS!';
            // $datiStrutturati = $dom->getElementsByTagName('itemtype');
            // $xpath = new DomXpath($dom);

            // foreach ($xpath->query('//[@itemtype="http://schema.org/Product"]') as $rowNode) {
            //     echo $rowNode->nodeValue; // will be 'this item'
            // }

            // echo '<table class="table table-striped table-hover">';
            // echo '<tr><th>Type</th><th>Value</th></tr>';
            // foreach($datiStrutturati as $datoStrutturato){
            //     echo $datoStrutturato->nodeValue;
            // }
            // echo '</table>';
        }

        return response()->json(['count' => $count_structured_data, 'response' => $output]);
    }
}
