<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Station;
use App\Models\CrossOverStation;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use Illuminate\Http\Request;

use Exception;


class TripController extends Controller
{
    public function getAllTripsFilteredByStartEndStations(Request $request){
        
        // get start and end station ids and handle not found station
        try{
            $start_station_id = Station::where('station_name', $request->start_station)->first()->id;
            $end_station_id = Station::where('station_name', $request->end_station)->first()->id;
        }
        catch(Exception $e){
            return response()->json(['error' => 'No such station'], 404);
        }

        // get all trips that start from the start station or end at the end station
        $trips_filtered_by_start_end_stations = CrossOverStation::where(function($query) use($start_station_id, $end_station_id){
            $query->where('start_station_id', '=', $start_station_id)->orWhere('end_station_id', '=', $end_station_id);
        })->get();

        // group segments by trip id
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
                if($start_station_order <= $end_station_order){
                    if((count($trip_segments) > 1 || ($trip_segment->start_station_id == $start_station_id && $trip_segment->end_station_id == $end_station_id)) && $trip_segments[0]->available_seats > 0){
                        $trips_filtered_with_multiple_segments[] = $trip_segments;
                    }
                }
            }
            
        });
        

        // handle edge cases
        // handle no trips found
        if(count($trips_filtered_with_multiple_segments) == 0){
            return response()->json(['error' => 'No trips found'], 404);
        }

        // format available trips response
        $available_trips = array();
        foreach($trips_filtered_with_multiple_segments as $trip_segments){
            $start_station_trip_id = Trip::where('id', $trip_segments[0]->trip_id)->first()->start_station_id;
            $end_station_trip_id = Trip::where('id', $trip_segments[0]->trip_id)->first()->end_station_id;
            $start_station_name = Station::find($start_station_trip_id)->station_name;
            $end_station_name = Station::find($end_station_trip_id)->station_name;

            $available_trips[] = [
                'trip_id' => $trip_segments[0]->trip_id,
                'bus_id' => Trip::where('id', $trip_segments[0]->trip_id)->first()->bus_id,
                'start_station' => $start_station_name,
                'end_station' => $end_station_name,
                'available_seats' => $trip_segments[0]->available_seats,
                'start_trip_order' => $trip_segments[0]->station_order,
                'end_trip_order' => $trip_segments[count($trip_segments) - 1]->station_order + 1,
            ];
        }
        return response()->json($available_trips);    
    }

    public function bookTrip(Request $request){
        $available_trips = $this->getAllTripsFilteredByStartEndStations($request);
        if($available_trips->getStatusCode() == 404){
            return response()->json(['error' => 'No trips found'], 404);
        }
        $trip = $available_trips->getData()[0];

        $trip_segments = CrossOverStation::where('trip_id', $trip -> trip_id)->get();
        // check available seats
        foreach($trip_segments as $trip_segment){
            $station_order = $trip_segment->station_order;
            if($station_order >= $trip->start_trip_order && $station_order <= $trip->end_trip_order){
                if($trip_segment->available_seats == 0){
                    return response()->json(['error' => 'No available seats'], 404);
                }
        }
        }

        // decrement only the segments in the range

        foreach($trip_segments as $trip_segment){
            $station_order = $trip_segment->station_order;
            if($station_order >= $trip->start_trip_order && $station_order < $trip->end_trip_order){
                $trip_segment->available_seats = $trip_segment->available_seats - 1;
                $trip_segment->save();
            }
            
        }
        return response()->json(['success' => 'Trip booked successfully'], 200);
    }
}
