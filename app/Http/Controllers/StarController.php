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
        return response()->json($star);
    }

    ##### Get the average value
    public function averageStar() {
        $stars  = Star::all();
        $countStars = number_format($stars->avg('vote'), 2);
        $averageStars = $stars->count();
        return response()->json(['count' => $averageStars, 'average' => $countStars]);
    }

    public function stepLink(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        ##################################################################################################################################
        $site = parse_url($url); // get the array of the url

        $base_url = $site['scheme'].'://'.$site['host']; // build the base_url for later

        ###### CONNECTION
        $auth = 0;
        $client = new \GuzzleHttp\Client([ 'http_errors' => false ]);
        if( $auth_username != '' && $auth_password != '' ) {
            $response = $client->request('GET', $url, ['auth' =>  [$auth_username, $auth_password]]);
            $auth = 1;
        } else {
            $response = $client->request('GET', $url, ['allow_redirects' => false]);
        }
        $httpcode = $response->getStatusCode();


        if( $httpcode == 200 ) { // only if the url is correct

            $dom = new DOMDocument;
            @$dom->loadHTML(mb_convert_encoding($response->getBody()->getContents(), 'HTML-ENTITIES', 'UTF-8'));

            ################################################################################################################################################ LINKS
            $links = $dom->getElementsByTagName('a');
            $response = '<table class="table table-striped table-hover">';
            $response .= '<tr><th>Href</th><th>Internal text</th><th>Title</th><th>Target</th><th>Rel</th><th>Class</th><th>ID</th></tr>';

            $count = count($links);
            foreach ($links as $link) {
                
                $class = $title = '';
                
                $href = $link->getAttribute('href');
                $rel = $link->getAttribute('rel');

                // add base url in case the link does not have it
                if( strpos($href,$base_url) === false ) { // base url non found
                    if( !filter_var($href, FILTER_VALIDATE_URL) ) { // is not an url
                        $href = $base_url.$href;
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
                    $image_path = (strpos($img['src'],$base_url) !== false ? $img['src'] : $base_url.$img['src']); // add base url in case the image does not have it
                    $internalText = '<a href="'.$image_path.'" target="_blank" title="Open image"><img src="'.$image_path.'" style="max-width:200px;"></a>';
                }
                    
                } else // the internal text is text
                    $internalText = $link->nodeValue;
                
                if($titolo == ''){
                    $class='notitle';
                    $title='Missing title tag';
                }
                
                $response .= '<tr class="'.$class.'" title="'.$title.'"><td style="word-wrap: break-word; max-width:400px;"><a href="'.$href.'" target="_blank" title="Visit the page">'.$href.'</a></td><td>'.$internalText.'</td><td>'.$titolo.'</td><td>'.$link->getAttribute('target').'</td><td>'.$rel.'</td><td>'.$link->getAttribute('class').'</td><td>'.$link->getAttribute('id').'</td></tr>';
            }
            $response .= '</table>';
        }
            ############################################################################################################################

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepImage(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepHeading(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepMeta(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepRobots(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepSitemap(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepOthers(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepStructuredData(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepErrors(Request $request) {
        $data = $request->all();

        $response = '';
        $count = 0;

        $url = $data['url'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }
}
