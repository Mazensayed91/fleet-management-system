<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Station;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Models\CrossOverStation;
use Illuminate\Http\Request;


class TripController extends Controller
{
    public function getAllTrips(Request $request){
        
        // get start and end station ids
        $start_station_id = Station::where('station_name', $request->start_station)->first()->id;
        $end_station_id = Station::where('station_name', $request->end_station)->first()->id;
        
        // get all trips that start from the start station or end at the end station
        $trips_filtered_by_start_end_stations = CrossOverStation::where(function($query) use($start_station_id, $end_station_id){
            $query->where('start_station_id', '=', $start_station_id)->orWhere('end_station_id', '=', $end_station_id);
        })->get();

        // filter trips that contains only the start station or end station
        $trips_filtered = array();

        foreach($trips_filtered_by_start_end_stations as $trip_segment)
        { 
            $trips_filtered[$trip_segment->trip_id][] = $trip_segment;
        }

        // filter trips_filtered by trips that have more than one segment and has available seats and start order
        $trips_filtered_with_multiple_segments = array();
        array_filter($trips_filtered, function($trip_segments) use(&$trips_filtered_with_multiple_segments, $start_station_id, $end_station_id){
            
            foreach($trip_segments as $trip_segment){
                if($trip_segment -> start_station_id == $start_station_id){
                    $start_station_order = $trip_segment->station_order;
                }
                if($trip_segment -> end_station_id == $end_station_id){
                    $end_station_order = $trip_segment->station_order;
                }
            }
            if(isset($start_station_order) && isset($end_station_order)){
                if($start_station_order < $end_station_order){
                    if(count($trip_segments) > 1 && $trip_segments[0]->available_seats > 0){
                        $trips_filtered_with_multiple_segments[] = $trip_segments;
                    }
                }
            }
            
        });

        return response()->json($trips_filtered_with_multiple_segments);    
    }
}
