<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class Route extends Model
{
    public static $routeDistance1;
    public static $routeDuration1;
    public static $data_arr;

    public static function searchRoute(){

        //$i = 20;
        //$j = 5;

        if(isset( $_POST['submit'])) {
            /*if (!empty($_POST['originsId'])) {
                $i = $_POST['originsId'];
            } else {
                return redirect()->back()->with('message','Поле не заполнено');
            }

            if (!empty($_POST['destinationsId'])) {
                $j = $_POST['destinationsId'];
            } else {
                return redirect()->back()->with('message','Поле не заполнено');
            }*/

            $i = $_POST['originsId'];
            $j = $_POST['destinationsId'];

            $route = DB::select('select distance, duration from route where origin_id = ? and destination_id = ?', [$i, $j]);
            //$routeOriginId = $route[0]->originId;
            //$routeDestinationId = $route[0]->destinationId;
            $routeDistance = $route[0]->distance;
            $routeDuration = $route[0]->duration;

            if (!empty($routeDistance && $routeDuration)) {
                $routeDistance1 = round($routeDistance / 1000, 1, PHP_ROUND_HALF_UP);
                $routeDuration1 = round($routeDuration / 60, 0, PHP_ROUND_HALF_UP);
                $data_arr = array();

                array_push(
                    $data_arr,
                    $routeDistance1,
                    $routeDuration1
                );

                //$data_arr = json_encode($data_arr1);

                var_dump($data_arr[0]);
                //exit;
                //return redirect()->route('route', 'RouteController@index')->with($routeDistance1)->with($routeDuration1);
                //return redirect()->back()->withInput();//->with($data_arr);//->with($routeDuration1);
                return Redirect::back()->withInput()->with($data_arr);
                //return Redirect::back()->withInput()->with('routeDuration1', $data_arr[1]);
                //return \redirect('viewroute')->with($data_arr);



            }else{
                $origins = DB::select('select lat, lng from locality where id = ?', [$i]);
                $originsId = $i;
                $originsLat = $origins[0]->lat;
                $originsLng = $origins[0]->lng;

                $destinations = DB::select('select lat, lng from locality where id = ?', [$j]);
                $destinationsId = $j;
                $destinationsLat = $destinations[0]->lat;
                $destinationsLng = $destinations[0]->lng;

                $api = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&language=ru&origins={$originsLat},{$originsLng}&destinations={$destinationsLat},{$destinationsLng}&key=AIzaSyBRupxmv_FiFu_vArjYd6em98mTvowpjLU");
                $resp = json_decode($api, true);

                // response status will be 'OK', if able to geocode given address
                if ($resp['status'] == 'OK') {

                    // get the important data
                    $routeDistance = isset($resp['rows'][0]['elements'][0]['distance']) ? $resp['rows'][0]['elements'][0]['distance']['value'] : "";
                    $routeDistance1 = round($routeDistance / 1000, 1, PHP_ROUND_HALF_UP);

                    //(int)$data->rows[0]->elements[0]->distance->value / 1000

                    $routeDuration = isset($resp['rows'][0]['elements'][0]['duration']) ? $resp['rows'][0]['elements'][0]['duration']['value'] : "";
                    $routeDuration1 = round($routeDuration / 60, 0, PHP_ROUND_HALF_UP);

                    DB::insert('insert into route (origin_id, destination_id, distance, duration) values (?, ?, ?, ?)', [$originsId, $destinationsId, $routeDistance, $routeDuration]);

                    // verify if data is complete
                    //if($routeDistance1 && $routeDuration1){

                    // put the data in the array
                    $data_arr = array();

                    array_push(
                        $data_arr,
                        $routeDistance1,
                        $routeDuration1
                    );

                    return $data_arr;

                    //var_dump($data_arr);
                    //end;

                    //}else{
                    //    return false;
                    // }
                    //return redirect()->route('route')->with($data_arr);//->with($routeDuration1);

                } else {
                    echo "<strong>ERROR: {$resp['status']}</strong>";
                    return false;
                }

            }
        }


    }
}
