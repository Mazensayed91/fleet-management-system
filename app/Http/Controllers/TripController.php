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
            return response()->json(['error' => '404', 'message' => 'Station not found'], 404);
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
            return response()->json(['error' => '404', 'message' => 'No trips found'], 404);
        }

        // format available trips response
        $available_trips = array();
        foreach($trips_filtered_with_multiple_segments as $trip_segments){
            $start_station_trip_id = Trip::where('id', $trip_segments[0]->trip_id)->first()->start_station_id;
            $end_station_trip_id = Trip::where('id', $trip_segments[0]->trip_id)->first()->end_station_id;
            $start_station_name = Station::find($start_station_trip_id)->station_name;
            $end_station_name = Station::find($end_station_trip_id)->station_name;

            // available seats is the minimum available seats of all segments
            $available_seats = min(array_map(function($trip_segment){
                return $trip_segment->available_seats;
            }, $trip_segments));
            // ignore trips with 0 available seats
            if($available_seats == 0){
                continue;
            }
            $available_trips[] = [
                'trip_id' => $trip_segments[0]->trip_id,
                'bus_id' => Trip::where('id', $trip_segments[0]->trip_id)->first()->bus_id,
                'start_station' => $start_station_name,
                'end_station' => $end_station_name,
                'available_seats' => $available_seats,
                'start_trip_order' => $trip_segments[0]->station_order,
                'end_trip_order' => $trip_segments[count($trip_segments) - 1]->station_order + 1,
            ];
        }
        return response()->json($available_trips);    
    }

    public function bookTrip(Request $request){
        $available_trips = $this->getAllTripsFilteredByStartEndStations($request);
        if($available_trips->getStatusCode() == 404){
            return response()->json(['error' => '404', 'message' => 'No trips found'], 404);
        }

        // replacing default keys with trip ids to make it easy to find the trip
        $array_index = 0;
        $available_trips_data = $available_trips->getData();
        foreach($available_trips_data as $available_trip){
            $available_trips_data[$available_trip->trip_id]= $available_trip;
            unset($available_trips_data[$array_index]);
            $array_index++;   
        }

        // check if the trip id is available
        $trip_id_exists = array_key_exists($request->trip_id, $available_trips_data);
        if(!$trip_id_exists){
            return response()->json(['error' => '404', 'message' => 'No trips found with this id, fetch trips to find a suitable trip id'], 404);
        }
        
        // set $trip to -1 if there is no avaialable trips
        $trip = array_column($available_trips_data, null, 'trip_id')[$request->trip_id] ?? -1;

        // condition to handle no available seats condition
        if(is_integer($trip)){
            if($trip == -1){
                return response()->json(['error' => '404', 'message' => 'No available seets'], 404);
            }
         }
        $trip_segments = CrossOverStation::where('trip_id', $trip -> trip_id)->get();

        // check available seats
        foreach($trip_segments as $trip_segment){
            $station_order = $trip_segment->station_order;
            if($station_order >= $trip->start_trip_order && $station_order < $trip->end_trip_order){
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
