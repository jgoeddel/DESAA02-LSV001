<?php
$curl_session = curl_init();
curl_setopt($curl_session, CURLOPT_URL, "https://172.27.49.109:40405/api/screenshot");
curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curl_session, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl_session, CURLOPT_HTTPHEADER, array(
        'content-type: image/png',
        'accept: */*',
        'Authorization: Basic cGJhZG1pbjowODVlY2M2Nzg=')
);
$result = curl_exec($curl_session);
var_dump($result);
curl_close($curl_session);
print_r($result);