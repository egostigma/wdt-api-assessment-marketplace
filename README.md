# wdt-api-assessment-marketplace-docker-app

Open API specification can be found here:

[collection](https://github.com/egostigma/wdt-api-assessment-marketplace/blob/main/openAPI/wdt-api-assessment-marketplace.postman_collection.json)\
[environment](https://github.com/egostigma/wdt-api-assessment-marketplace/blob/main/openAPI/wdt-api-assessment-marketplace.postman_environment.json)

## Usage
----

**Register**
----

  Returns new user and access token.

* **URL**

  https://wdt-api-assessment-marketplace.herokuapp.com/api/v1/auth/register

* **Method:**

  `POST`
  
* **URL Params**

    None

* **Data Params**

   **Required:**

   `email=[email]`\
   `phone=[string]`\
   `name=[string]`\
   `address=[string]`\
   `password=[string]`\
   `password_confirmation=[string]`

* **Success Response:**

  * **Code:** 200 OK\
    **Content:** `{
    "data": {
        "user": {
            "email": "email@email.com",
            "phone": "085712345678",
            "name": "Name",
            "address": "address",
            "updated_at": "2022-07-10T04:48:49.000000Z",
            "created_at": "2022-07-10T04:48:49.000000Z",
            "id": 12
        },
        "access_token": "xxx"
    },
    "messages": [
        "Success"
    ],
    "error": false
}`

* **Error Response:**

  * **Code:** 422 Unprocessable Content\
    **Content:** `{
    "data": null,
    "messages": [
        "Email ini sudah terdaftar.",
        "Phone ini sudah terdaftar.",
        "Konfirmasi password tidak cocok."
    ],
    "error": true
}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://wdt-api-assessment-marketplace.herokuapp.com/api/v1/auth/register",
      dataType: "json",
      data: {
        email: "email@email.com", 
        phone: "085712345678",
        name: "Name",
        address: "address",
        password: "password",
        password_confirmation: "password",
      },
      type : "POST",
      success : function(r) {
        console.log(r);
      }
    });
  ```

**Login**
----

  Returns logged in user and access token.

* **URL**

  https://wdt-api-assessment-marketplace.herokuapp.com/api/v1/auth/login

* **Method:**

  `POST`
  
* **URL Params**

    None

* **Data Params**

   **Required:**

   `email=[email]`\
   `password=[string]`

* **Success Response:**

  * **Code:** 200 OK\
    **Content:** `{
    "data": {
        "user": {
            "id": 11,
            "name": "Nama",
            "email": "email@email.com",
            "phone": "085712345678",
            "address": "address",
            "email_verified_at": null,
            "remember_token": null,
            "created_at": "2022-07-09T05:33:51.000000Z",
            "updated_at": "2022-07-09T05:33:51.000000Z"
        },
        "access_token": "xxx"
    },
    "messages": [
        "Success"
    ],
    "error": false`

* **Error Response:**

  * **Code:** 401 Unauthorized\
    **Content:** `{
        "data": null,
        "messages": [
            "Unauthenticated."
        ],
        "error": true
    }`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://wdt-api-assessment-marketplace.herokuapp.com/api/v1/auth/login",
      dataType: "json",
      data: {
        email: "email@email.com", 
        password: "password",
      },
      type : "POST",
      success : function(r) {
        console.log(r);
      }
    });
  ```
