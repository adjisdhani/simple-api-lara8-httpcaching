# Simple Laravel 8 Caching with HTTP Caching

This is a Simple Laravel 8 Caching with HTTP Caching with endpoints accessible via RESTful API.

## Features
- View all products (GET `/api/products`)

## Requirements
- PHP 7.4 or higher
- Composer
- Laravel 8
- MySQL or MariaDB

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/adjisdhani/simple-api-lara8-httpcaching.git
   ```

2. **Navigate to the project directory**:
   ```bash
   cd simple-api-lara8-httpcaching
   ```

3. **Install dependencies**:
   ```bash
   composer install
   ```

4. **Generate the application key**:
   ```bash
    php artisan key:generate
    ```

5. **Start the development server**:
   ```bash
    php artisan serve
    ```

6. **Access the API**:
   (http://127.0.0.1:8000/api/employees)

      ## API Endpoints 
    
    **1. Get All Data**

    - Method: GET
    - Endpoint: /api/products
    - Description: Retrieve a list of all products.

    **Example Response**:
    
      [
          {
              "id": 1,
              "name": "Product A",
              "price": 10000
          },
          {
              "id": 2,
              "name": "Product B",
              "price": 20000
          }
      ]
7. **Double klik index.html if you want testing a caching client side**
8. **Penjelasan soal cachingnya**
   ## balikan dari apinya 

   **1. lihat balikan dari apinya , itu memuat data dan cache expirednya , saat ini kita set ke 1 detik expirednya + ada header khusus untuk taruh value cachingnya yaitu Etag**
     ```bash
      return response()->json($products, 200, [
            'Cache-Control' => 'max-age=1, public',
            'ETag' => $etag,
        ]);
     ```

    **2. di sini ada pengecekan sebelumnya di apinya , kalo yang request headernya menambahkan "If-None-Match" dan value tersebut masih sama dengan cache yang terakhirnya yaitu Etag maka dibalikin 304 responnya**
    ```bash
      $products = [
            ['id' => 1, 'name' => 'Product A', 'price' => 10000],
            ['id' => 2, 'name' => 'Product B', 'price' => 20000],
        ];

        $timestamp = now()->timestamp;
        $timeWindow = floor($timestamp / 1);

        $etag = hash('sha256', json_encode($products) . $timeWindow);

        $clientEtag = $request->headers->get('If-None-Match');
        if ($clientEtag && $clientEtag === $etag) {
            return response()->json(null, 304);
        }
    ```

    **3. dan dari sisi client sidenya yaitu yang di index.html itu ada perbedaan status ketika cachingnya masih belom expired dan expired, di client sidenya untuk value Etagnya dan data productnya disimpen di dalam local storage**

    ```bash
      const cachedETag = localStorage.getItem('etag');
        const headers = cachedETag ? { 'If-None-Match': cachedETag, 'Content-Type': 'application/json' } : {'Content-Type': 'application/json'};

        const response = await fetch('http://127.0.0.1:8000/api/products', {
          method: 'GET',
          headers: headers,
          mode: 'cors',
          credentials: 'same-origin',
      });

        // Handle 304 Not Modified
        if (response.status === 304) {
            document.getElementById('status').innerHTML = "Data loaded from cache.";
            const cachedData = localStorage.getItem('products');
            return JSON.parse(cachedData);
        }

        if (response.status === 200) {

            const data = await response.json();
            const etag = response.headers.get('ETag');

            localStorage.setItem('etag', etag);
            localStorage.setItem('products', JSON.stringify(data));
            document.getElementById('status').innerHTML = "Data loaded from server.";
            return data;
        }

        document.getElementById('status').innerHTML = `Error: ${response.status}`;
    ```

    **4. selesai untuk caching menggunakan http caching + local storage di sisi client sidenya**

## Author
Adjis Ramadhani Utomo

## License
This project is licensed under the [MIT license](https://opensource.org/licenses/MIT).