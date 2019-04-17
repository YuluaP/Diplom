<?php

namespace App\Geo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Direction extends Model
{
    public static $directionDistance1;
    public static $directionDuration1;
    public static $data_arr;

    /**
     * Looking for routes, their distance and duration with Google Maps directions API, and saving into DB
     * @param $originId
     * @param $destinationId
     * @return array|bool
     */
    public static function searchRoute($originId, $destinationId){

        $direction = DB::select('select distance, duration from direction where origin_id = ? and destination_id = ?', [$originId, $destinationId]);

        /**
         * Converts a number to a time format.
         * @param $time
         * @param string $format
         * @return string
         */
        function convertToHoursMins($time, $format = '%02d ч. %02d мин.') {
            if ($time < 1) {
                return $time;
            }
            $hours = floor($time / 60);
            $minutes = ($time % 60);
            return sprintf($format, $hours, $minutes);
        }

        if (!empty($direction)) {

            $directionDistance = $direction[0]->distance;
            $directionDuration = $direction[0]->duration;

            $directionDistance1 = round($directionDistance / 1000, 1, PHP_ROUND_HALF_UP);
            $directionDuration1 = round($directionDuration / 60, 0, PHP_ROUND_HALF_UP);

            return ['directionDistance1' => $directionDistance1, 'directionDuration1'=> convertToHoursMins($directionDuration1)];

        }else{
            $origin = DB::select('select lat, lng from locality where id = ?', [$originId]);
            $originLat = $origin[0]->lat;
            $originLng = $origin[0]->lng;

            $destination = DB::select('select lat, lng from locality where id = ?', [$destinationId]);
            $destinationLat = $destination[0]->lat;
            $destinationLng = $destination[0]->lng;

            $api = file_get_contents("https://maps.googleapis.com/maps/api/directions/json?language=ru&origin={$originLat},{$originLng}&destination={$destinationLat},{$destinationLng}&key=AIzaSyBRupxmv_FiFu_vArjYd6em98mTvowpjLU");
            $resp = json_decode($api, true);

                // response status will be 'OK', if able to geocode given address
            if ($resp['status'] == 'OK') {

                    // get the important data
                $directionDistance = isset($resp['routes'][0]['legs'][0]['distance']) ? $resp['routes'][0]['legs'][0]['distance']['value'] : "";
                $directionDistance1 = round($directionDistance / 1000, 1, PHP_ROUND_HALF_UP);

                $directionDuration = isset($resp['routes'][0]['legs'][0]['duration']) ? $resp['routes'][0]['legs'][0]['duration']['value'] : "";
                $directionDuration1 = round($directionDuration / 60, 0, PHP_ROUND_HALF_UP);

                $directionWay = isset($resp['routes'][0]['overview_polyline']['points']) ? $resp['routes'][0]['overview_polyline']['points'] : "";

                DB::insert('insert into direction (origin_id, destination_id, distance, duration, way) values (?, ?, ?, ?, ?)', [$originId, $destinationId, $directionDistance, $directionDuration, $directionWay]);

            } else {
                echo "<strong>ERROR: {$resp['status']}</strong>";
                return false;
            }

            return ['directionDistance1' => $directionDistance1, 'directionDuration1'=> convertToHoursMins($directionDuration1)];
        }

    }
}
