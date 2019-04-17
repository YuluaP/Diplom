<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Geo\UserDirection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserDirectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {

            $user_id = Auth::user()->id;
        }

        $userDirections = UserDirection::where('user_id', $user_id)->orderBy('updated_at', 'desc')->paginate(5);

        return view('geo.userDirections', ['userDirections' => $userDirections]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('geo.addDirectionTest');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'directionName' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect('directions/add')
                ->withErrors($validator)
                ->withInput();
        }

        if (Auth::check()) {

            $user_id = Auth::user()->id;
        }
        $directionName = $request->input('directionName');
        $originAddress = $request->input('originAddress');
        $originLat = $request->input('originLat');
        $originLng = $request->input('originLng');
        $destinationAddress = $request->input('destinationAddress');
        $destinationLat = $request->input('destinationLat');
        $destinationLng = $request->input('destinationLng');
        if (!empty($request->input('private'))) {
            $private = true;
        }else{
            $private = false;
        }

        if (!empty($request->input('placeComment'))) {
            $comment = $request->input('placeComment');
        }else{
            $comment = null;
        }


        DB::insert('insert into user_directions (user_id, direction_name, origin_address, origin_lat, origin_lng,
            destination_address, destination_lat, destination_lng, private, comment) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$user_id, $directionName, $originAddress, $originLat, $originLng, $destinationAddress, $destinationLat,
                $destinationLng, $private, $comment]);

        return redirect('directions');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $direction = DB::select('select direction_name, origin_lat, origin_lng, destination_lat, destination_lng,
            comment, private from user_directions where id = ?', [$id]);
        $directionName = $direction[0]->direction_name;
        $originAddress = $direction[0]->origin_address;
        $originLat = $direction[0]->origin_lat;
        $originLng = $direction[0]->origin_lng;
        $destinationAddress = $direction[0]->destination_address;
        $destinationLat = $direction[0]->destination_lat;
        $destinationLng = $direction[0]->destination_lng;
        $directionComment = $direction[0]->comment;
        $directionPrivate = $direction[0]->private;


        return view('geo.editDirection', ['$directionId' => $id, 'directionName' => $directionName,
            'originAddress' => $originAddress, 'originLat' => $originLat, 'originLng' => $originLng,
            'destinationAddress' => $destinationAddress, 'destinationLat' => $destinationLat,
            'destinationLng' => $destinationLng, 'directionComment' => $directionComment,
            'directionPrivate' => $directionPrivate]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $directionId = $request->input('directionId');
        $rules = array(
            'directionName' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('places.edit', ['id' => $directionId])
                ->withErrors($validator)
                ->withInput();
        }
        $directionName = $request->input('directionName');
        $originLat = $request->input('originLat');
        $originLng = $request->input('originLng');
        $destinationLat = $request->input('destinationLat');
        $destinationLng = $request->input('destinationLng');
        if (!empty($request->input('private'))) {
            $private = true;
        }else{
            $private = false;
        }

        if (!empty($request->input('placeComment'))) {
            $comment = $request->input('directionComment');
        }else{
            $comment = null;
        }

        DB::update('update user_directions set direction_name = ?, origin_lat = ?, origin_lng = ?, 
            destination_lat = ?, destination_lng = ?, private = ?, comment = ? where id = ?',
            [$directionName, $originLat, $originLng, $destinationLat, $destinationLng, $private, $comment, $directionId]);

        return redirect('directions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('user_directions')->where('id', $id)->delete();

        return redirect('directions');
    }

    public function search(Request $request)
    {
        $user_id = Auth::user()->id;

        $search = $request->get('term');

        /*$result = DB::table('locality')
            ->select(DB::raw("id, CONCAT(concat(name_ua, ', '),
            (IF(area_id is NULL, concat(''), concat((SELECT area.name_ua FROM `area` WHERE area_id = area.id), ' р-н, '))),
             (SELECT region.name_ua FROM `region` WHERE region_id = region.id), ' обл.') as name, lat, lng"))
            ->where('locality.name_ua', 'LIKE', '%'. $search. '%')
            ->get();*/

        $result = DB::table('place')
        ->select(DB::raw("id, place_name as name, lat, lng"))
        ->where('place_name', 'LIKE', '%'. $search. '%')
        ->where('user_id',  $user_id)
        ->get();

        return response()->json($result);

    }
}
