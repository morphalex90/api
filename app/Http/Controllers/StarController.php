<?php
 
namespace App\Http\Controllers;
 
use App\Star;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
class StarController extends Controller {

    ##### Create new star
    public function createStar(Request $request) {
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

        $response = 'Links bla bla bla';
        $count = 25;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepImage(Request $request) {
        $data = $request->all();

        $response = 'Image bla bla bla';
        $count = 40;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepHeading(Request $request) {
        $data = $request->all();

        $response = 'Heading bla bla bla';
        $count = 40;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepMeta(Request $request) {
        $data = $request->all();

        $response = 'Meta bla bla bla';
        $count = 40;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepRobots(Request $request) {
        $data = $request->all();

        $response = 'Robots bla bla bla';
        $count = 40;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepSitemap(Request $request) {
        $data = $request->all();

        $response = 'Sitemap bla bla bla';
        $count = 40;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepOthers(Request $request) {
        $data = $request->all();

        $response = 'Others bla bla bla';
        $count = 40;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepStructuredData(Request $request) {
        $data = $request->all();

        $response = 'StructuredData bla bla bla';
        $count = 40;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }

    public function stepErrors(Request $request) {
        $data = $request->all();

        $response = 'Errors bla bla bla';
        $count = 40;

        $url = $data['url'];
        $id = $data['id'];
        $auth_username = $data['auth_username'];
        $auth_password = $data['auth_password'];

        return response()->json(['count' => $count, 'response' => $response]);
    }
}
