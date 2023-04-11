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

function connect_db()
{
    // Database credentials
    $username = "doadmin";
    $password = "AVNS_8vHG9GOXC3YYJuUy7ST";
    $host = "db-mysql-nyc1-75307-do-user-13792118-0.b.db.ondigitalocean.com";
    $port = "25060";
    $database = "defaultdb";
    $sslmode = "REQUIRED";

    // Create connection
    $conn = mysqli_init();
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    mysqli_real_connect($conn, $host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);

    // Check connection
    if (mysqli_connect_errno()) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        echo "success";
    }

    // Connection successful, continue with your queries here
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

// run the "onboarding_csrf" function, and store the result into "$onboarding_requirements" variable
$onboarding_requirements = onboarding_csrf();

// if there is an error in getting the csrf, it will print the error message on the browser, or else the request will continue
//if ($onboarding_requirements['error'] === TRUE) {
//    echo json_encode($onboarding_requirements['message']);
//} else {
//    print_r($onboarding_requirements);
//}

connect_db();

