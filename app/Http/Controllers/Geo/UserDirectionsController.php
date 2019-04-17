<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Geo\UserDirection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $userDirections = UserDirection::where('user_id', Auth::user()->id)->orderBy('updated_at', 'desc')->paginate(3);

        return view('geo.userDirectionsTest', ['userDirections' => $userDirections]);
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
            'origin_address' => Rule::unique('user_directions')->where(function ($query){
                $query->where('destination_address', request()->input('destinationAddress'))
                    ->where('user_id', Auth::user()->id);
            })
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect('directions/add')
                ->withErrors($validator)
                ->withInput();
        }

        $directionName = $request->input('directionName');
        $originAddress = $request->input('origin_address');
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

        if (!empty($request->input('directionComment'))) {
            $comment = $request->input('directionComment');
        }else{
            $comment = null;
        }

        $distance = $request->input('clearDistance');
        $duration = $request->input('clearDuration');

        DB::insert('insert into user_directions (user_id, direction_name, origin_address, origin_lat, origin_lng,
            destination_address, destination_lat, destination_lng, private, comment, distance, duration) values 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [Auth::user()->id, $directionName, $originAddress, $originLat, $originLng,
            $destinationAddress, $destinationLat, $destinationLng, $private, $comment, $distance, $duration]);

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
        $direction = DB::select('select direction_name, origin_address, origin_lat, origin_lng, destination_address, 
            destination_lat, destination_lng, comment, private, distance, duration from user_directions where id = ?', [$id]);
        $directionName = $direction[0]->direction_name;
        $originAddress = $direction[0]->origin_address;
        $originLat = $direction[0]->origin_lat;
        $originLng = $direction[0]->origin_lng;
        $destinationAddress = $direction[0]->destination_address;
        $destinationLat = $direction[0]->destination_lat;
        $destinationLng = $direction[0]->destination_lng;
        $directionComment = $direction[0]->comment;
        $directionPrivate = $direction[0]->private;
        $distance = $direction[0]->distance;
        $duration = $direction[0]->duration;

        return view('geo.editDirectionTest', ['directionId' => $id, 'directionName' => $directionName,
            'originAddress' => $originAddress, 'originLat' => $originLat, 'originLng' => $originLng,
            'destinationAddress' => $destinationAddress, 'destinationLat' => $destinationLat,
            'destinationLng' => $destinationLng, 'directionComment' => $directionComment,
            'directionPrivate' => $directionPrivate, 'clearDistance' => $distance, 'clearDuration' => $duration]);
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
            'origin_address' => Rule::unique('user_directions')->where(function ($query){
                $query->where('destination_address', request()->input('destinationAddress'))->where('user_id', Auth::user()->id);
            })->ignore($directionId,'id')
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('directions.edit', ['id' => $directionId])
                ->withErrors($validator)
                ->withInput();
        }
        $directionName = $request->input('directionName');
        $originAddress = $request->input('origin_address');
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

        if (!empty($request->input('directionComment'))) {
            $comment = $request->input('directionComment');
        }else{
            $comment = null;
        }
        $distance = $request->input('clearDistance');
        $duration = $request->input('clearDuration');

        DB::update('update user_directions set direction_name = ?, origin_address = ?, origin_lat = ?, origin_lng = ?, 
            destination_address = ?, destination_lat = ?, destination_lng = ?, private = ?, comment = ?, distance = ?, 
            duration = ? where id = ?', [$directionName, $originAddress, $originLat, $originLng, $destinationAddress,
            $destinationLat, $destinationLng, $private, $comment, $distance, $duration, $directionId]);

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
        $search = $request->get('term');

        $result = DB::table('place')
        ->select(DB::raw("id, place_name as name, lat, lng"))
        ->where('place_name', 'LIKE', '%'. $search. '%')
        ->where('user_id',  Auth::user()->id)
        ->get();

        return response()->json($result);
    }
}
