<?php

namespace App\Http\Controllers\Geo;

use App\Geo\Direction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DirectionController extends Controller
{
    /**
     * Shows the page with the search for routes, their distance and duration
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */

    public function index(Request $request)
    {
        $directionDistance = Null;
        $directionDuration = Null;
        $origin = Null;
        $originId = Null;
        $destination = Null;
        $destinationId = Null;


        if ($request->isMethod('post')) {

            $rules = array (
                'origin' => 'required',
                'destination' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()){
                return redirect('direction')
                    ->withErrors($validator)
                    ->withInput();
            }

            $origin = $request->input('origin');
            $destination = $request->input('destination');
            $originId = $request->input('originId');
            $destinationId = $request->input('destinationId');

            $dir = Direction::searchRoute($originId, $destinationId);
            $directionDistance = $dir['directionDistance1'];
            $directionDuration = $dir['directionDuration1'];


        }

        return view('geo.direction', ['origin' => $origin, 'originId' => $originId, 'destination' => $destination, 'destinationId' => $destinationId, 'directionDistance1' => $directionDistance, 'directionDuration1' => $directionDuration]);
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
             (SELECT region.name_ua FROM `region` WHERE region_id = region.id), ' обл.') as name"))
            ->where('locality.name_ua', 'LIKE', '%'. $search. '%')
            ->get();

        return response()->json($result);

    }
}
