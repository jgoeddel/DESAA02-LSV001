<?php
/*
$fields = http_build_query(array(
    "id" => "e28b0cb4-e2a8-46ab-8cf1-7ac7055f70d0",
));
$curl_session = curl_init();
curl_setopt($curl_session, CURLOPT_URL, "https://172.27.49.109:40405/api/peakboards");
curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curl_session, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl_session, CURLOPT_POSTFIELDS, $fields);
curl_setopt($curl_session, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic cGJhZG1pbjowODVlY2M2Nzg=')
);
$result = curl_exec($curl_session);
var_dump($result);
curl_close($curl_session);
print_r($result);

*/
 //step1
$curl_session = curl_init();
//step2
curl_setopt($curl_session ,CURLOPT_URL,"https://172.27.49.109:40405/api/screenshot");
curl_setopt($curl_session, CURLOPT_HTTPHEADER, array(
        'content-type: image/png',
        'accept: */*',
        'Authorization: Basic cGJhZG1pbjowODVlY2M2Nzg=')
);
//step3
$result = curl_exec($curl_session );
var_dump($result);
//step4
curl_close($curl_session );
//step5
echo $result;
