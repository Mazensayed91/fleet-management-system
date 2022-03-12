# Rakabny

Rakabny is a fleet management system that aims to make it easy for each person to book his trip seat!

## Technology Stack
- Language: PHP 8.1.2
- Frameworks: Laravel 9
- DB: SQLite
- Authentication: Sanctum
- Unit-Testing: PHPUnit

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


## Database
![Untitled Workspace (1)](https://user-images.githubusercontent.com/54520113/157956845-f10a1bf3-cc61-4803-b578-fdd85e4adf08.png)
