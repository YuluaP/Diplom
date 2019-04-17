<?php

namespace App\Http\Controllers\Geo;

use App\Geo\Place;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlaceController extends Controller
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
        $places = Place::where('user_id', Auth::user()->id)->orderBy('updated_at', 'desc')->paginate(5);

        return view('geo.userPlacesTest', ['places' => $places]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('geo.addPlaceTest');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = Auth::user()->id;

        $rules = array(
            'placeName' => 'required',
            'placeAddress' => 'unique:place,address,NULL,id,user_id,'.Auth::user()->id.'',
           );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect('places/add')
                ->withErrors($validator)
                ->withInput();
        }


        $placeName = $request->input('placeName');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
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
        $placeAddress = $request->input('placeAddress');

        DB::insert('insert into place (user_id, place_name, lat, lng, private, comment, address) 
            values (?, ?, ?, ?, ?, ?, ?)', [$user_id, $placeName, $lat, $lng, $private, $comment, $placeAddress]);

        return redirect('places');
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
        $place = DB::select('select place_name, lat, lng, private, comment, address from place where id = ?', [$id]);
        $placeName = $place[0]->place_name;
        $placeLat = $place[0]->lat;
        $placeLng = $place[0]->lng;
        $placePrivate = $place[0]->private;
        $placeComment = $place[0]->comment;
        $placeAddress = $place[0]->address;

        return view('geo.editPlaceTest', ['placeId' => $id, 'placeName' => $placeName, 'placeLat' => $placeLat,
            'placeLng' => $placeLng, 'placePrivate' => $placePrivate, 'placeComment' => $placeComment,
            'placeAddress' =>$placeAddress]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $placeId = $request->input('placeId');
        $rules = array(
            'placeName' => 'required',
            'placeAddress' => 'unique:place,address,'.$placeId.',id,user_id,'.Auth::user()->id,
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('places.edit', ['id' => $placeId])
                ->withErrors($validator)
                ->withInput();
        }
        $placeName = $request->input('placeName');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
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
        $placeAddress = $request->input('placeAddress');

        DB::update('update place set place_name = ?, lat = ?, lng = ?, private = ?, comment = ?, address = ? 
            where id = ?', [$placeName, $lat, $lng, $private, $comment, $placeAddress, $placeId]);

        return redirect('places');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        DB::table('place')->where('id', $id)->delete();

        return redirect('places');
    }

    /**
     * Autocomplete search function
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function search(Request $request)
    {
        $search = $request->get('term');

        $result = DB::table('locality')
            ->select(DB::raw("id, CONCAT(concat(name_ua, ', '),
            (IF(area_id is NULL, concat(''), concat((SELECT area.name_ua FROM `area` WHERE area_id = area.id), ' р-н, '))),
             (SELECT region.name_ua FROM `region` WHERE region_id = region.id), ' обл.') as name, lat, lng"))
            ->where('locality.name_ua', 'LIKE', '%'. $search. '%')
            ->get();

        return response()->json($result);

    }
}
