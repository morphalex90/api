<?php
 
namespace App\Http\Controllers;
 
use App\Star;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DOMDocument;
use SimpleXMLElement;
 
class StarController extends Controller {

    ##### Create new star
    public function createStar(Request $request) {

        $this->validate($request, [
            'vote' => 'required|numeric|min:1|max:5'
        ]);

        $star = Star::create($request->all());

        ## Mail to myself
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Info <info@morpheus90.com>' . "\r\n";
        $content ='Stars: '.$star->vote.'<br>';
        mail('piero.nanni@gmail.com','Tools By Piero Nanni - New Vote', $content, $headers);

        return response()->json($star);
    }

    ##### Get the average value
    public function averageStar() {
        $stars  = Star::all();
        $countStars = number_format($stars->avg('vote'), 2);
        $averageStars = $stars->count();
        return response()->json(['count' => $averageStars, 'average' => $countStars]);
    }

    public function checkUrl($data) {
        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        $site = parse_url($url); // get the array of the url
        $base_url = $site['scheme'].'://'.$site['host']; // build the base_url for later

        $auth = 0;
        $client = new \GuzzleHttp\Client([ 'http_errors' => false ]);
        if( $auth_username != '' && $auth_password != '' ) {
            $response = $client->request('GET', $url, ['auth' =>  [$auth_username, $auth_password]]);
            $auth = 1;
        } else {
            $response = $client->request('GET', $url, ['allow_redirects' => false]);
        }

        if( $response->getStatusCode() == 200 ) { // only if the url is correct

            $dom = new DOMDocument;
            @$dom->loadHTML(mb_convert_encoding($response->getBody()->getContents(), 'HTML-ENTITIES', 'UTF-8'));

            return [
                'dom' => $dom,
                'base_url' => $base_url,
                'auth' => $auth,
            ];
        } else {
            return;
        }
    }

    public function stepLink(Request $request) {
        $data = $request->all();
        $output = '';
        $info = $this->checkUrl($data);

        ## Mail to myself
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Info <info@morpheus90.com>' . "\r\n";
        $content ='Website: '.$data['url'].'<br>';
        mail('piero.nanni@gmail.com','Tools By Piero Nanni - New Search', $content, $headers);

        if( $info ) {

            // $auth = $info['auth'];

            $links = $info['dom']->getElementsByTagName('a');
            $output = '<table>';
            $output .= '<tr><th>Href</th><th>Internal text</th><th>Title</th><th>Target</th><th>Rel</th><th>Class</th><th>ID</th></tr>';

            foreach ($links as $link) {
                
                $class = $title = '';
                
                $href = $link->getAttribute('href');
                $rel = $link->getAttribute('rel');

                // add base url in case the link does not have it
                if( strpos($href,$info['base_url']) === false ) { // base url non found
                    if( !filter_var($href, FILTER_VALIDATE_URL) ) { // is not an url
                        $href = $info['base_url'].$href;
                    }
                }

                // internal text
                $internalText = '';
                $titolo = $link->getAttribute('title');

                if( trim($link->nodeValue) == '' ){ // if there is no text inside, search for images or other (using trim function because there might be spaces or tabs)
                    
                    $img['title'] = '';
                    $img['src'] = '';

                    if( $link->childNodes->length > 1 ) { // loop inside the child only if there is content 
                        foreach( $link->childNodes as $child ){
                            // echo '<pre>'.print_r($child,1).'</pre>';
                            if( $child->nodeName == 'img' ){ // get the src only for the images
                                $img['src'] = $child->getAttribute('src');
                                break;
                            }
                        }
                    }

                if( $img['src'] != '' ) {
                    $image_path = (strpos($img['src'],$info['base_url']) !== false ? $img['src'] : $info['base_url'].$img['src']); // add base url in case the image does not have it
                    $internalText = '<a href="'.$image_path.'" target="_blank" title="Open image"><img src="'.$image_path.'" style="max-width:200px;"></a>';
                }
                    
                } else // the internal text is text
                    $internalText = $link->nodeValue;
                
                if($titolo == ''){
                    $class='notitle';
                    $title='Missing title tag';
                }
                
                $output .= '<tr class="'.$class.'" title="'.$title.'"><td style="word-wrap: break-word; max-width:400px;"><a href="'.$href.'" target="_blank" title="Visit the page">'.$href.'</a></td><td>'.$internalText.'</td><td>'.$titolo.'</td><td>'.$link->getAttribute('target').'</td><td>'.$rel.'</td><td>'.$link->getAttribute('class').'</td><td>'.$link->getAttribute('id').'</td></tr>';
            }
            $output .= '</table>';
        }
            ############################################################################################################################

        return response()->json(['count' => count($links), 'response' => $output]);
    }

    public function stepImage(Request $request) {
        
        $data = $request->all();
        $output = '';
        $info = $this->checkUrl($data);

        if( $info ) {

            // $auth = $info['auth'];

            $imgs = $info['dom']->getElementsByTagName('img');

            $output .= '<table>';
            $output .= '<tr><th>Image</th><th>Data Src</th><th>Alt</th><th>Title</th><th>Height</th><th>Width</th><th>Class</th><th>ID</th></tr>';
            foreach ($imgs as $img) {  
                $class = $titolo = $href= '';
                if( $img->getAttribute('alt') == '' ){
                    $class='noalt';
                    $titolo='Missing alt tag';
                }   
                $href = $img->getAttribute('src');
                $output .= '<tr class="'.$class.'" title="'.$titolo.'"><td><a href="'.(stripos($href, $info['base_url']) !== false ? $href : $info['base_url'].$href).'" target="_blank"><img src="'.(stripos($href,$info['base_url']) !== false ? $href : $info['base_url'].$href).'" style="max-width:300px;"></a></td><td>'.$img->getAttribute('data-src').'</td><td>'.$img->getAttribute('alt').'</td><td>'.$img->getAttribute('title').'</td><td>'.$img->getAttribute('height').'</td><td>'.$img->getAttribute('width').'</td><td>'.$img->getAttribute('class').'</td><td>'.$img->getAttribute('id').'</td></tr>';
            }
            $output .= '</table>';


        }


        return response()->json(['count' => count($imgs), 'response' => $output]);
    }

    public function stepHeading(Request $request) {
        
        $data = $request->all();
        $output = '';
        $info = $this->checkUrl($data);

        if( $info ) {

            // $auth = $info['auth'];

            $output = '<table>';
            $output .= '<tr><th>Type</th><th>Content</th><th>Class</th><th>ID</th></tr>';
            $count_headings = 0;
            $headings = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');

            foreach( $headings as $heading ) {
                $temps = $info['dom']->getElementsByTagName($heading);
                foreach ($temps as $temp) {
                    $output .= '<tr><td>'.strtoupper($heading).'</td><td>'.$temp->nodeValue.'</td><td>'.$temp->getAttribute('class').'</td><td>'.$temp->getAttribute('id').'</td></tr>';
                    $count_headings++;
                }
            }
        }

            $output .= '</table>';

        return response()->json(['count' => $count_headings, 'response' => $output]);
    }

    public function stepMeta(Request $request) {
        $data = $request->all();
        $output = '';
        $info = $this->checkUrl($data);

        if( $info ) {

            // $auth = $info['auth'];

            $metas = $info['dom']->getElementsByTagName('meta');
            $count_meta = 0;
            $output = '<table>';
            $output .= '<tr><th>Type</th><th>Label</th><th>Content</th></tr>';
            foreach ($metas as $meta) {
                
                $output .= '<tr>';
                
                if( $meta->getAttribute('property') != '' ) {
                    $output .= '<td>property</td><td>'.$meta->getAttribute('property').'</td>';
                    $count_meta++;
                }
                
                if( $meta->getAttribute('name') != '' ) {
                    $output .= '<td>name</td><td>'.$meta->getAttribute('name').'</td>';
                    $count_meta++;
                }
                
                if( $meta->getAttribute('itemprop') != '' ) {
                    $output .= '<td>itemprop</td><td>'.$meta->getAttribute('itemprop').'</td>';
                    $count_meta++;
                }
                
                if( $meta->getAttribute('http-equiv') != '' ) {
                    $output .= '<td>http-equiv</td><td>'.$meta->getAttribute('http-equiv').'</td>';
                    $count_meta++;
                }
                
                if( $meta->getAttribute('charset') != '' ) {
                    $output .= '<td></td><td>charset</td><td>'.$meta->getAttribute('charset').'</td>';
                    $count_meta++;
                }
                
                if( $meta->getAttribute('charset') == '' ) {

                    $overridden = 0;

                    // themecolor, use the color for showing the span
                    if( $meta->getAttribute('name') == 'theme-color' ) {
                        $output .= '<td><span style="color:'.$meta->getAttribute('content').';">'.$meta->getAttribute('content').'</span></td></tr>';
                        $overridden = 1;
                    }

                    // check if og:url is correct
                    if( $meta->getAttribute('property') == 'og:url' ) {
                        if( $meta->getAttribute('content') == $data['url'] || $meta->getAttribute('content') == $data['url'].'/' ) {
                            $output .= '<td><span style="color:green;">'.$meta->getAttribute('content').'</span></td></tr>'; 
                        } else {
                            $output .= '<td><span style="color:red;">'.$meta->getAttribute('content').'</span></td></tr>'; 
                        }
                        $overridden = 1;
                    }

                    // check if twitter:url is correct
                    if( $meta->getAttribute('name') == 'twitter:url' ) {
                        if( $meta->getAttribute('content') == $data['url'] || $meta->getAttribute('content') == $data['url'].'/' ) {
                            $output .= '<td><span style="color:green;">'.$meta->getAttribute('content').'</span></td></tr>'; 
                        } else {
                            $output .= '<td><span style="color:red;">'.$meta->getAttribute('content').'</span></td></tr>'; 
                        }
                        $overridden = 1;
                    }

                    // check if og:image is correct
                    if( $meta->getAttribute('property') == 'og:image' ) {
                        if( @getimagesize( $meta->getAttribute('content')) ) {
                            $output .= '<td><span style="color:green;">'.$meta->getAttribute('content').'</span></td></tr>'; 
                        } else {
                            $output .= '<td><span style="color:red;">'.$meta->getAttribute('content').'</span></td></tr>'; 
                        }
                        $overridden = 1;
                    }

                    // check if twitter:image is correct
                    if( $meta->getAttribute('name') == 'twitter:image' ) {
                        if( @getimagesize( $meta->getAttribute('content')) ) {
                            $output .= '<td><span style="color:green;">'.$meta->getAttribute('content').'</span></td></tr>'; 
                        } else {
                            $output .= '<td><span style="color:red;">'.$meta->getAttribute('content').'</span></td></tr>'; 
                        }
                        $overridden = 1;
                    }

                    # DEFAULT
                    if( !$overridden ) {
                        $output .= '<td>'.$meta->getAttribute('content').'</td></tr>';
                    }
                    
                    $count_meta++;
                }
            }
            $output .= '</table>';
        }

        return response()->json(['count' => $count_meta, 'response' => $output]);
    }

    public function stepRobots(Request $request) {
        $data = $request->all();
        $output = '';
        $info = $this->checkUrl($data);

        if( $info ) {

            // $auth = $info['auth'];

            $client = new \GuzzleHttp\Client([ 'http_errors' => false ]);
            if( $info['auth'] == 1 ) {
                $response = $client->request('GET', $info['base_url'].'/robots.txt', ['auth' =>  [$data['auth_username'], $data['auth_password']]]);
            } else {
                $response = $client->request('GET', $info['base_url'].'/robots.txt', ['allow_redirects' => false]);
            }
            if( $response->getStatusCode() == 200) {
                $robots = $response->getBody()->getContents();
                $output .= ( $robots != null ? '<pre>'.($robots).'</pre>' : 'Empty robots.txt' );

                if( $robots == null ) {
                    $$output .= 'Empty robots.txt';
                }
            } else {
                $$output .= 'Robots.txt not found (error '.$response->getStatusCode().')';
            }
        }

        return response()->json(['response' => $output]);
    }

    public function stepSitemap(Request $request) {
        $data = $request->all();
        $output = '';
        $info = $this->checkUrl($data);

        if( $info ) {

            // $auth = $info['auth'];

            $client = new \GuzzleHttp\Client([ 'http_errors' => false ]);
            if( $info['auth'] == 1 ) {
                $response = $client->request('GET', $info['base_url'].'/sitemap.xml', ['auth' =>  [$data['auth_username'], $data['auth_password']]]);
            } else {
                $response = $client->request('GET', $info['base_url'].'/sitemap.xml', ['allow_redirects' => true]);
            }

            if( $response->getStatusCode() == 200) {
                $sitemap_response = $response->getBody()->getContents();
                $document = new DOMDocument;
                $document->loadXML($sitemap_response);
                $sitemap = $document->saveXML();

                if( $sitemap != null && $sitemap != '<!--?xml version="1.0"?-->' ){
                    $xml = new SimpleXMLElement($sitemap);
                    $print = (htmlentities($xml->asXML()));
                    $output .= '<pre>'.str_replace('  ','&nbsp;&nbsp;',$print).'</pre>';
                } else {
                    $output .= 'Sitemap not found';
                }
            } else {
                $output .= 'sitemap.xml not found (error '.$response->getStatusCode().')';
            }
        }

        return response()->json(['response' => $output]);
    }

    public function stepOthers(Request $request) {
        $data = $request->all();
        $output = '';
        $info = $this->checkUrl($data);

        if( $info ) {

            // $auth = $info['auth'];

            $count_others = 0;
            $linksCanonical = $info['dom']->getElementsByTagName('link');
 
            $output .= '<table>';
            $output .= '<tr><th>Type</th><th>Value</th></tr>';
            foreach($linksCanonical as $linkCanonical){
                if( $linkCanonical->getAttribute('rel') == 'canonical' ) {
                    $canonical = (strpos($linkCanonical->getAttribute('href'), $data['url']) !== false ? $linkCanonical->getAttribute('href') : $info['base_url'].$linkCanonical->getAttribute('href'));
                    if( $canonical == $data['url'] || $canonical == $data['url'].'/' ) {
                        $output .= '<tr><td>Canonical</td><td><span style="color:green;">'.$canonical.'</span></td></tr>';
                    } else {
                        $output .= '<tr><td>Canonical</td><td><span style="color:red;">'.$canonical.'</span></td></tr>';
                    }
                    $count_others++;
                }
                
                if( $linkCanonical->getAttribute('rel') == 'alternate' && $linkCanonical->getAttribute('hreflang') != '' ) {
                    $output .= '<tr><td>Hreflang ('.$linkCanonical->getAttribute('hreflang').')</td><td>'.$linkCanonical->getAttribute('href').'</td></tr>';
                    $count_others++;
                }
            }
            $output .= '</table>';
        }

        return response()->json(['count' => $count_others, 'response' => $output]);
    }

    public function stepStructuredData(Request $request) {
        $data = $request->all();
        $output = '';
        $info = $this->checkUrl($data);

        if( $info ) {

            // $auth = $info['auth'];

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
