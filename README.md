# Rakabny <img src="https://user-images.githubusercontent.com/54520113/158013941-aeb34c8f-b3f1-47d7-b789-5be3a8ae00ca.png" width="50" height="30">

Rakabny is a fleet management system that aims to make it easy for each person to book his trip seat!

## Technology Stack
- Language: PHP 8.1.2
- Frameworks: Laravel 9
- DB: SQLite
- Authentication: Sanctum
- Unit-Testing: PHPUnit

## Prerequisites

- PHP 8
- Composer

## Getting Started

- Clone the repo ```https://github.com/Mazensayed91/fleet-management-system.git```
- cd fleet-management-system
- Install composer packages ```composer install```
- Install NPM packages ```npm install```
- Create database.sqlite file inside the database folder
- php artisan migrate  
- php artisan db:seed --class=DatabaseSeeder
- php artisan serve



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

```
    {
        "error": "404",
        "message": "No trips found with this id, fetch trips to find a suitable trip id"
    }
```
```
    {
        "error": "401"
        "message": "Unauthenticated."
    }
```
Example errors the user may incounter using Rakabny:

* 400 – bad request
* 404 – resource not found
* 401 – Unauthenticated

### Endpoints

#### GET /api/trips

* General: Returns a list trips that fall in a given range.
* Sample: `curl http://localhost:8000/trips`<br>
* Message body:
```
         {
	        "start_station": "Cairo",
	        "end_station": "Asyut"
        } 
```
* Message Header:
  ```
         {
	        "Authorization": your bearer token,
            "Content-Type": "application/json",
            "Accept": "application/json"
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
* Sample: `curl http://localhost:8000/book`<br>
* Message body:
  ```
         {
	        "start_station": "Cairo",
	        "end_station": "Asyut",
            "trip_id": 4
        }
  ```
* Message Header:
  ```
         {
	        "Authorization": your bearer token,
            "Content-Type": "application/json",
            "Accept": "application/json"
         }
  ```
* Return:
  ```
    {
	    "success": "Trip booked successfully"
    }
   ```

#### POST /api/register

* General:
* Sample: `curl http://localhost:8000/register`<br>
* Message body:
  ```
         {
            "name": "Mazen",
            "email": "mazen99@gmail.com",
            "password": "mazen@gmail.com",
            "password_confirmation": "mazen@gmail.com"
         }
  ```
* Return:
  ```
        {
            "success": "Trip booked successfully"
        }
   ```
 * If user already exists:
   ```
       {
            "message": "Email already exists"
       }
   ```
   
#### POST /api/login

* General:
* Sample: `curl http://localhost:8000/loign`<br>
* Message body:
  ```
        {
            "email": "mazen@gmail.com",
            "password": "mazen@gmail.com"
        }
  ```
* Return:
  ```
    {
        "user": {
            "id": 1,
            "name": "Mazen",
            "email": "mazen@gmail.com",
            "email_verified_at": null,
            "created_at": "2022-03-11T22:29:14.000000Z",
            "updated_at": "2022-03-11T22:29:14.000000Z"
	    },
	    "token": a bearer token
    }
   ```
   
#### POST /api/logout

* General:
* Sample: `curl http://localhost:8000/book`<br>
* Message Header:
  ```
         {
	        "Authorization": your bearer token
         }
  ```
* Return:
  ```
    {
        "message": "Logged out",
        "status": 200
    }
   ```
## Unit Testing
I believe that unit tests are a one of the best ways to document the project. To learn what functionality is provided by one module or another, developers can refer to unit tests to get a basic picture of the logic of the module and the system as a whole. 

Unit testing in this project is seprated into 3 main test specs to test (Authentication - Fetching Trips - Booking)

To run all unit tests: ```php artisan test```


#### Authentication Unit Testing
| UnitTest                        |                  Description                 | Expected Return |
| ------------------------------- | ---------------------------------------------|-----------------|
|test_register_happy_scenario     | Tests creating account with valid data       |     201         |
|test_register_existing_email     | Tests creating account with duplicate data   |     404         |

#### Booking Unit Testing
| UnitTest                           |                  Description                 | Expected Return |
| ---------------------------------- | ---------------------------------------------|-----------------|
|test_booking_without_authentication | Tests booking without authentication         |     401         |
|test_booking_happy_scenario         | Tests fetching trips using Bearer token      |     200         |
|test_booking_no_available_seet      | Tests fetching trips without available seets |     404         |
|test_booking_non_existing_trip      | Tests booking with a non existing stations   |     404         |
|test_booking_non_existing_trip_id   | Tests booking with a non existing trip id    |     404         |


#### Trip Fetching Unit Testing

| UnitTest                                  |                  Description                     | Expected Return |
| ------------------------------------------| -------------------------------------------------|-----------------|
|test_fetching_trips_without_authentication | Tests fetching trips without authentication      |     401         |
|test_fetching_trips_happy_scenario         | Tests fetching trips using Bearer token          |     200         |
|test_fetching_trips_non_existing_stations  | Tests fetching trips with a non exsiting stations|     404         |



## Database
![Untitled Workspace (1)](https://user-images.githubusercontent.com/54520113/157956845-f10a1bf3-cc61-4803-b578-fdd85e4adf08.png)

## What Next?

- Add dates to trips
- Add details to the busses like driver name and estimated time using this bus
- Deploy the application
