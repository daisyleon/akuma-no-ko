<?php

// this "generate_useragent" function is working to get one user agent from an array list
function generate_useragent()
{
    $ua = [
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36 Avast/111.0.20716.147",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36 Edg/112.0.1722.34",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36 Edg/111.0.1661.62",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/111.0",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36 OPR/97.0.0.0",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36"
    ];

    $random_key = array_rand($ua);
    $random_user_agent = $ua[$random_key];

    return $random_user_agent;
}

function get_address()
{
    // Database credentials
    $username = "doadmin";
    $password = "AVNS_8vHG9GOXC3YYJuUy7ST";
    $host = "db-mysql-nyc1-75307-do-user-13792118-0.b.db.ondigitalocean.com";
    $port = "25060";
    $database = "defaultdb";
    $sslmode = "REQUIRED";

    // Create connection
    $conn = mysqli_connect($host, $username, $password, $database, $port, $sslmode);

    // Check if the connection is successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve data from "address" table
    $sql = "SELECT * FROM address WHERE is_used = false LIMIT 1";
    $result = $conn->query($sql);

    // Check for errors
    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    // Fetch the result as an associative array
    $row = $result->fetch_assoc();

    $data = explode(" ", $row['address']);

    if (count($data) == 7) {
        $address = $data[0] . " " . $data[1] . " " . $data[2];
        $city = $data[3];
    } elseif (count($data) == 8) {
        $address = $data[0] . " " . $data[1] . " " . $data[2] . " " . $data[3];
        $city = $data[4];
    } elseif (count($data) == 9) {
        $address = $data[0] . " " . $data[1] . " " . $data[2] . " " . $data[3] . " " . $data[4];
        $city = $data[5];
    }


    $sql = "UPDATE address SET is_used = TRUE WHERE id = " . $row['id'];

    if (mysqli_query($conn, $sql)) {
        $return['error'] = FALSE;
        $return['address'] = trim($address);
        $return['city'] = trim($city);
        $return['state'] = "TX";
    } else {
        $return['error'] = TRUE;
        $return['message'] = mysqli_error($conn);
    }

    mysqli_close($conn);

    return $return;
}

function geo_location($address, $city, $state)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCMYD1LH5BHndirgjdRVrfi0LxxSJnawnU&address=' . urlencode($address . ", " . $city . ", " . $state),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $location_json = json_decode($response, true);
    return $location_json;
}

// This "onboarding_csrf" function is working to get the csrf token to be used in the next request
function onboarding_csrf()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => 'https://www.lemonade.com/onboarding',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: _lmnd_enable_cookies=true; _lmnd_geography=US; _lmnd_uuid_2021=569cf75c-61ba-4c50-8275-c2ed3194f5f3; _lmnd_rails_session=MFlvM3p4ZTBIZzhMNkVDZVZQbFNxRUpKYXVmWHRIbGNNeWJ0RDdXR2RramsrMDNwZFFpWkpaZ2JUTFhyVE1UcTloWFk4ZUJ5OGVqY3M1dmx3Qld3RzV4aG5jd2czVUVrb3lYYjJDMHJRekF2NUFCTllJTDh1WE0yakIrNjhPZTN2TFVCTTFscTBJV2w5MzkxQURzcHpaNFpqY3doQVYzd0ppK3NCOXFnZlIzOHMyYTZWdHJwUEJwL09weDMrS29XSUEzcVBRYXhJTm9zMytucDFmcDhXRENOMDRpZWx5VTl2TGczbUEwaURDdz0tLVRQakhlVDNwQTEzSzRTVm44WDF0RVE9PQ%3D%3D--0a923e4663204aca0beb3268c1d5309b42ca83a8; _lmnd_referral=%7B%22value%22%3A%7B%22uuid%22%3A%22569cf75c-61ba-4c50-8275-c2ed3194f5f3%22%2C%22referrer%22%3Anull%2C%22resource_id%22%3Anull%2C%22controller%22%3A%22onboarding%22%2C%22action%22%3A%22index%22%2C%22id%22%3A%22LR663B2592D3%22%2C%22created_at%22%3A1681132379%7D%7D',
            'user-agent: ' . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Onboarding CSRF'
        ];
    } else {
        if ($http_code == 200) {
            $onboarding_csrf_token = explode('" />', explode('name="csrf-token" content="', $response)[1])[0];
            $data = [
                'error' => FALSE,
                'csrf' => $onboarding_csrf_token
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Onboarding CSRF'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function init_chat($cookie, $csrf)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => 'https://www.lemonade.com/onboarding/init_chat',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "{}",
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "referer: https://www.lemonade.com/",
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: " . $csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Init Chat'
        ];
    } else {
        if ($http_code == 200) {
            $data = [
                'error' => FALSE,
                'message' => $response
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Init Chat'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function onboarding_one($cookie, $csrf, $script_id)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => "https://www.lemonade.com/onboarding/" . $script_id . "/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"user":{"question_id":"has_account","answers":[{"attribute":"has_account","content":"no_account"}],"script_id":"' . $script_id . '","script_identifier":"onboarding_guest"}}',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "referer: https://www.lemonade.com/",
            'sec-ch-ua: "Microsoft Edge";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: " . $csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Onboarding One'
        ];
    } else {
        if ($http_code == 200) {
            $data = [
                'error' => FALSE,
                'message' => $response
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Onboarding One'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function car_csrf($cookie)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => 'https://www.lemonade.com/car/1?f=1',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HTTPHEADER => array(
            "referer: https://www.lemonade.com/",
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: document",
            "sec-fetch-mode: navigate",
            "sec-fetch-site: same-origin",
            "sec-fetch-user: ?1",
            "upgrade-insecure-requests: 1",
            'user-agent: ' . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Car CSRF'
        ];
    } else {
        if ($http_code == 200) {
            $car_csrf_token = explode('" />', explode('name="csrf-token" content="', $response)[1])[0];
            $data = [
                'error' => FALSE,
                'csrf' => $car_csrf_token
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Car CSRF'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function car_init($cookie, $csrf)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => 'https://www.lemonade.com/car/quotes/init_chat',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"restart":true,"identifier":"car_wizard"}',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "referer: https://www.lemonade.com/",
            'sec-ch-ua: "Microsoft Edge";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: " . $csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Car Init'
        ];
    } else {
        if ($http_code == 200) {
            $data = [
                'error' => FALSE,
                'message' => $response
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Car Init'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function car_quote($cookie, $csrf, $script_id, $firstname, $lastname)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => "https://www.lemonade.com/car/quotes/" . $script_id . "/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"user":{"script_id":"' . $script_id . '","script_identifier":"car_wizard","question_id":"user_name","answers":[{"attribute":"first_name","content":"' . $firstname . '"},{"attribute":"last_name","content":"' . $lastname . '"}]}}',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "referer: https://www.lemonade.com/",
            'sec-ch-ua: "Microsoft Edge";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: " . $csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Car Quote'
        ];
    } else {
        if ($http_code == 200) {
            $data = [
                'error' => FALSE,
                'message' => $response
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Car Quote'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function car_quote_second($cookie, $csrf, $script_id, $formatted_address, $placeid, $latitude, $longitude, $zip, $state, $street, $streetnumber, $city)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => "https://www.lemonade.com/car/quotes/" . $script_id . "/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"user":{"script_id":"' . $script_id . '","script_identifier":"car_wizard","question_id":"address","answers":[{"attribute":"address_details","content":"' . $formatted_address . '"},{"attribute":"address","content":{"place_id":"' . $placeid . '","lat":' . $latitude . ',"lng":' . $longitude . ',"postal_code":"' . $zip . '","state":"' . $state . '","street":"' . $street . '","street_number":"' . $streetnumber . '","city":"' . $city . '","country":"US","details":"' . $formatted_address . '"}}]}}',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "referer: https://www.lemonade.com/",
            'sec-ch-ua: "Microsoft Edge";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: " . $csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Car Quote Second'
        ];
    } else {
        if ($http_code == 200) {
            $data = [
                'error' => FALSE,
                'message' => $response
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Car Quote Second'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function dob_quote($cookie, $csrf, $script_id, $email, $month, $day, $year)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => "https://www.lemonade.com/car/quotes/" . $script_id . "/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"user":{"script_id":"' . $script_id . '","script_identifier":"car_wizard","question_id":"user_details","answers":[{"attribute":"user_email","content":"' . $email . '"},{"attribute":"date_of_birth","content":{"month":"' . $month . '","day":"' . $day . '","year":"' . $year . '"}},{"attribute":"terms_of_service_approved","content":true}]}}',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "referer: https://www.lemonade.com/",
            'sec-ch-ua: "Microsoft Edge";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: " . $csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : DOB Quote'
        ];
    } else {
        if ($http_code == 200) {
            $data = [
                'error' => FALSE,
                'message' => $response
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : DOB Quote'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function scanner_quote($cookie, $script_id, $csrf) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => "https://www.lemonade.com/car/quotes/".$script_id."/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"user":{"script_id":"'.$script_id.'","script_identifier":"car_wizard","question_id":"explanation","answers":[]}}',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "referer: https://www.lemonade.com/",
            'sec-ch-ua: "Microsoft Edge";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: ".$csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Scanner Quote'
        ];
    } else {
        if ($http_code == 200) {
            $data = [
                'error' => FALSE,
                'message' => $response
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Scanner Quote'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function grab_v1($cookie, $script_id, $csrf) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => "https://www.lemonade.com/car/household/discovery_data/".$script_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HEADER => TRUE,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "referer: https://www.lemonade.com/",
            'sec-ch-ua: "Microsoft Edge";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: ".$csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Grab V1 Quote'
        ];
    } else {
        if ($http_code == 200) {
            $header_string = explode("CF-RAY:", $response);
            $json_response = trim($header_string[1]);
            $pos = strpos($json_response, "\n");
            $jsonBody = substr($json_response, $pos + 1);
            $json = json_decode($jsonBody, true);
            $etag = explode("Set-Cookie:", explode("ETag: ", $header_string[0])[1])[0];
            $json['etag'] = $etag;
            $data = [
                'error' => FALSE,
                'message' => $json
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Grab V1 Quote'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function grab_v2($cookie, $script_id, $csrf, $etag){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io:7777',
        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:1B2b3b4b5b',
        CURLOPT_URL => "https://www.lemonade.com/car/household/discovery_data/".$script_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json;charset=UTF-8",
            "origin: https://www.lemonade.com",
            "if-none-match: ".$etag,
            "referer: https://www.lemonade.com/",
            'sec-ch-ua: "Microsoft Edge";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: \"Windows\"",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "timezone: America/New_York",
            "x-come-join-us: makers.lemonade.com",
            "x-csrf-token: ".$csrf,
            "user-agent: " . generate_useragent()
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $data = [
            'error' => TRUE,
            'message' => curl_error($curl) . ' | STEP : Grab V2 Quote'
        ];
    } else {
        if ($http_code == 200) {
            $data = [
                'error' => FALSE,
                'message' => $response
            ];
        } else {
            $data = [
                'error' => TRUE,
                'message' => $http_code . ' | STEP : Grab V2 Quote'
            ];
        }
    }

    curl_close($curl);
    return $data;
}

function response_builder_by_subject($json, $firstname, $lastname) {
    $objects = [];
//    $data = [
//        "name" => '',
//        "gender" => '',
//        "dob" => '',
//        "dl" => '',
//        "dlstate" => '',
//        "issue" => '',
//        "expiry" => '',
//        "class" => ''
//    ];
    foreach($json['data']['household_data']['subjects'] as $key => $value) {
        $data["name"] = $value["first_name"]." ".$value["last_name"];

        if ($value["gender"] != null) {
            $data["gender"] = $value["gender"];
        } else {
            $data["gender"] = '';
        }

        if ($value["birth_date"] == null) {
            $data["dob"] = '';
        } else {
            if ($value["birth_date"]["month"] != null && $value["birth_date"]["day"] != null && $value["birth_date"]["year"] != null) {
                $data["dob"] = $value["birth_date"]["month"]."/".$value["birth_date"]["day"]."/".$value["birth_date"]["year"];
            } else {
                $data["dob"] = '';
            }
        }

        if ($value["driver_license_id"] != null) {
            $data["dl"] = $value["driver_license_id"];
        } else {
            $data["dl"] = '';
        }

        if ($value["driver_license_state"] != null) {
            $data["dlstate"] = $value["driver_license_state"];
        } else {
            $data["dlstate"] = '';
        }

        if ($value["driver_license_issue_date"] != null) {
            $data["issue"] = $value["driver_license_issue_date"];
        } else {
            $data["issue"] = '';
        }

        if ($value["driver_license_expiration_date"] != null) {
            $data["expiry"] = $value["driver_license_expiration_date"];
        } else {
            $data["expiry"] = '';
        }

        if (strtolower($value['first_name']) == strtolower($firstname) && strtolower($value['last_name']) == strtolower($lastname)) {
            $data["class"] = "subject";
        } else {
            $data["class"] = "family";
        }
        array_push($objects, $data);
    }
    return $objects;
}


// Gather all the requirement to send the post request

$type = $_POST['type'];

if ($type == "subject") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $firstname . $lastname . uniqid() . '@gmail.com';
    if (strpos($email, '-') !== false) {
        $email = str_replace('-', '', $email);
    } else {
        $email = $email;
    }
    $month = $_POST['month'];
    $day = $_POST['day'];
    $year = $_POST['year'];
    $get_address = get_address();
    if ($get_address['error'] === TRUE) {
        echo json_encode($get_address);
        return false;
    } else {
        $geo_location_json = geo_location($get_address['address'], $get_address['city'], $get_address['state']);
        $formatted_address = $geo_location_json['results'][0]['formatted_address'];
        $latitude = $geo_location_json['results'][0]['geometry']['location']['lat'];
        $longitude = $geo_location_json['results'][0]['geometry']['location']['lng'];
        $placeid = $geo_location_json['results'][0]['place_id'];
        $streetnumber = $geo_location_json['results'][0]['address_components'][0]['short_name'];
        $street = $geo_location_json['results'][0]['address_components'][1]['short_name'];
        $state = $geo_location_json['results'][0]['address_components'][5]['short_name'];
        if (isset($geo_location_json['results'][0]['address_components'][7])) {
            $zip = $geo_location_json['results'][0]['address_components'][7]['short_name'];
        } else {
            $zip = $geo_location_json['results'][0]['address_components'][6]['short_name'];
        }
        $city = $geo_location_json['results'][0]['address_components'][3]['short_name'];
        $country = $geo_location_json['results'][0]['address_components'][6]['short_name'];
    }

// run the "onboarding_csrf" function, and store the result into "$onboarding_requirements" variable
    $onboarding_requirements = onboarding_csrf();

// if there is an error in getting the csrf, it will print the error message on the browser, or else the request will continue
    if ($onboarding_requirements['error'] === TRUE) {
        echo json_encode($onboarding_requirements['message']);
    } else {
        $cookie = substr(hash("sha512", uniqid()), 0, 15) . ".txt";
        $init_chat = init_chat($cookie, $onboarding_requirements['csrf']);
        if ($init_chat['error'] === TRUE) {
            echo json_encode($onboarding_requirements);
        } else {
            $init_chat_json_body = json_decode($init_chat['message'], true);
            $onboarding_one = onboarding_one($cookie, $onboarding_requirements['csrf'], $init_chat_json_body['script_id']);
            if ($onboarding_one['error'] === TRUE) {
                echo json_encode($onboarding_one);
            } else {
                $onboarding_one_json = json_decode($onboarding_one['message'], true);
                $car_csrf = car_csrf($cookie);
                if ($car_csrf['error'] === TRUE) {
                    echo json_encode($car_csrf);
                } else {
                    $car_init = car_init($cookie, $car_csrf['csrf']);
                    if ($car_init['error'] === TRUE) {
                        echo json_encode($car_init);
                    } else {
                        $car_init_json = json_decode($car_init['message'], true);
                        $car_quote = car_quote($cookie, $car_csrf['csrf'], $car_init_json['script_id'], $firstname, $lastname);
                        if ($car_quote['error'] === TRUE) {
                            echo json_encode($car_quote);
                        } else {
                            $car_quote_json = json_decode($car_quote['message'], true);
                            $second_car_quote = car_quote_second($cookie, $car_csrf['csrf'], $car_quote_json['script_id'], $formatted_address, $placeid, $latitude, $longitude, $zip, $state, $street, $streetnumber, $city);
                            if ($second_car_quote['error'] === TRUE) {
                                echo json_encode($second_car_quote);
                            } else {
                                $second_car_quote_json = json_decode($second_car_quote['message'], true);
                                $dob_quote = dob_quote($cookie, $car_csrf['csrf'], $car_quote_json['script_id'], $email, $month, $day, $year);
                                if ($dob_quote['error'] === TRUE) {
                                    echo json_encode($dob_quote);
                                } else {
                                    $scanner_quote = scanner_quote($cookie, $car_quote_json['script_id'], $car_csrf['csrf']);
                                    if ($scanner_quote['error'] === TRUE) {
                                        echo json_encode($scanner_quote);
                                    } else {
                                        $grab_v1 = grab_v1($cookie, $car_quote_json['script_id'], $car_csrf['csrf']);
                                        if ($grab_v1['error'] === TRUE) {
                                            echo json_encode($grab_v1);
                                        } else {
//                                        print_r($grab_v1);
//                                        $grab_v1_json = json_decode($grab_v1['message']);
                                            $grab_v2 = grab_v2($cookie, $car_quote_json['script_id'], $car_csrf['csrf'], $grab_v1['message']['etag']);
                                            if ($grab_v2['error'] === TRUE) {
                                                echo json_encode($grab_v2);
                                            } else {
                                                $grab_v2_json = json_decode($grab_v2['message'], true);
                                                if ($grab_v2_json['data']['discovery_session_status'] == "hit") {
                                                    $match_subject = FALSE;
                                                    foreach ($grab_v2_json['data']['household_data']['subjects'] as $value) {
                                                        if (strtolower($value['first_name']) == strtolower($firstname) && strtolower($value['last_name']) == strtolower($lastname)) {
                                                            $match_subject = TRUE;
                                                        }
                                                    }
                                                    if ($match_subject === TRUE) {
                                                        $build_response = response_builder_by_subject($grab_v2_json, $firstname, $lastname);
                                                        print_r($build_response);
                                                    } else {
                                                        $response['error'] = TRUE;
                                                        $response['message'] = "data not match";
                                                        $response['action'] = "retry";
                                                        echo json_encode($response);
                                                    }
                                                } else {
                                                    $response['error'] = TRUE;
                                                    $response['message'] = "status pending or not hit";
                                                    $response['action'] = "retry";
                                                    echo json_encode($response);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}



