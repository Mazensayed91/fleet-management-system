# Rakabny

Rakabny is a fleet management system that aims to make it easy for each person to book his trip seat!

## Technology Stack
- Language: PHP 8.1.2
- Frameworks: Laravel 9
- DB: SQLite

## API Reference

### Getting Started

* Base URL:This application is hosted locally at `http://localhost:8000/`. A public URL will be added here once the application is deployed.

### Error Handling

Errors are returned as JSON and are formatted in the following manner:<br>
```
    {
        "error": "404",
        "message": "No available seats"
    }
``` 
```
    {
        "error": "404",
        "message": "Station not found"
    }
```
Example errors the user may incounter using Rakabny:

* 400 – bad request
* 404 – resource not found

### Endpoints

#### GET /api/trips

* General: Returns a list trips that fall in a given range.
* Sample: `curl http://127.0.0.1:8000/trips`<br>
* Message body:
```
         {
	        "start_station": "Cairo",
	        "end_station": "Asyut"
        } 
```
* Return:
```
        [
            {
                "trip_id": 1,
                "bus_id": 6,
                "start_station": "Cairo",
                "end_station": "El-Auxor",
                "available_seats": 7,
                "start_trip_order": 1,
                "end_trip_order": 6
            },
            {
                "trip_id": 4,
                "bus_id": 5,
                "start_station": "Cairo",
                "end_station": "El-Giza",
                "available_seats": 12,
                "start_trip_order": 1,
                "end_trip_order": 5
            },
            {
                "trip_id": 6,
                "bus_id": 2,
                "start_station": "Cairo",
                "end_station": "Asyut",
                "available_seats": 12,
                "start_trip_order": 1,
                "end_trip_order": 5
            }
        ]
```

#### POST /api/book

* General:
  * Sample: `curl http://127.0.0.1:8000/book`<br>
  * Message body:
  ```
         {
	        "start_station": "Cairo",
	        "end_station": "Asyut",
            "trip_id": 4
        }
  ```
  * Return:
  ```
    {
	    "success": "Trip booked successfully"
    }
   ```


## Database
![Untitled Workspace (1)](https://user-images.githubusercontent.com/54520113/157956845-f10a1bf3-cc61-4803-b578-fdd85e4adf08.png)
