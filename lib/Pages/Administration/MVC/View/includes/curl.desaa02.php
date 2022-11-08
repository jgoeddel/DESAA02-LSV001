<?PHP
/*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
*/
$hostname = "172.27.49.109:40405";
$user = "pbadmin";
$pass = "085ecc678";
$url = "https://" . $hostname . "/Login?ReturnUrl=%2F";

function get_data($url) {

    $ch = curl_init();
    $timeout = 5;
    $username = 'pbadmin';
    $password = '085ecc678';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    #curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");    // Add This Line
    $data = curl_exec($ch);
    var_dump($data);
    curl_close($ch);
    return $data;
}
$url = "https://goeddel.info";
$data = get_data($url);
echo $data; die;
