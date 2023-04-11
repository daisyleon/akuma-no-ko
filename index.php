<?php

function onboarding_csrf() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PROXY => 'pr.oxylabs.io',
//        CURLOPT_PROXYUSERPWD => 'customer-Kordhell-cc-us:Jancok123@@@',
//        CURLOPT_URL => 'https://www.lemonade.com/onboarding',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: _lmnd_enable_cookies=true; _lmnd_geography=US; _lmnd_uuid_2021=569cf75c-61ba-4c50-8275-c2ed3194f5f3; _lmnd_rails_session=MFlvM3p4ZTBIZzhMNkVDZVZQbFNxRUpKYXVmWHRIbGNNeWJ0RDdXR2RramsrMDNwZFFpWkpaZ2JUTFhyVE1UcTloWFk4ZUJ5OGVqY3M1dmx3Qld3RzV4aG5jd2czVUVrb3lYYjJDMHJRekF2NUFCTllJTDh1WE0yakIrNjhPZTN2TFVCTTFscTBJV2w5MzkxQURzcHpaNFpqY3doQVYzd0ppK3NCOXFnZlIzOHMyYTZWdHJwUEJwL09weDMrS29XSUEzcVBRYXhJTm9zMytucDFmcDhXRENOMDRpZWx5VTl2TGczbUEwaURDdz0tLVRQakhlVDNwQTEzSzRTVm44WDF0RVE9PQ%3D%3D--0a923e4663204aca0beb3268c1d5309b42ca83a8; _lmnd_referral=%7B%22value%22%3A%7B%22uuid%22%3A%22569cf75c-61ba-4c50-8275-c2ed3194f5f3%22%2C%22referrer%22%3Anull%2C%22resource_id%22%3Anull%2C%22controller%22%3A%22onboarding%22%2C%22action%22%3A%22index%22%2C%22id%22%3A%22LR663B2592D3%22%2C%22created_at%22%3A1681132379%7D%7D'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}

onboarding_csrf();