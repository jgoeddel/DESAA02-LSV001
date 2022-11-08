<?PHP
/*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
*/
$hostname   = "cau-airwave.service.rhs.zz";
$user       = "apiview";
$pass       = "F9h6hy4U3O2D80t81BTRN3G9eiD5u6!";
$url        = "https://" . $hostname . "/ap_list.xml?ap_folder_id=10";
$retval     = getLoginToken($user,$pass,$hostname);
$retxml     = getData($hostname,$retval);


# Funktionen zum Abrufen der gewünschten Daten
function getLoginToken($user,$pass,$hostname) {
    $data =  http_build_query(
        array(
            'credential_0'  => $user,
            'credential_1' => $pass,
            'destination' => 'index.html'
        )
    );
    $opts  = array(
        'ssl'=>array(
            'verify_peer' => false,
            'verify_peer_name'=>false,
        ),'http'=>array(
            'method' => 'POST',
            'header'=> 'Content-type: application/x-www-form-urlencoded;charset=UTF-8',
            'content' => $data
        )
    );
    $context    = stream_context_create($opts);
    $url        = "https://" . $hostname . "/LOGIN";
    $ret        = file_get_contents($url,false,$context);
    $headers    = parseHeaders($http_response_header);
    return $headers;
}
function getData($hostname,$token) {
    $opts =  array(
        'ssl'=>array(
            'verify_peer' => false,
            "verify_peer_name"=>false
        ),
        'http'=>array(
            'method' => "GET",
            'header' => "Content-Type: application/xml\r\n" .
                "charset=UTF-8\r\n" .
                "X-BISCOTTI:" . $token["X-BISCOTTI"] . "\r\n" .
                "Cookie: " . $token["Set-Cookie"]
        )
    );
    $context     = stream_context_create($opts);
    $url        = "https://" . $hostname . "/ap_list.xml?ap_folder_id=10";
    $fgc        = file_get_contents($url,false,$context);
    $xml        = simplexml_load_string($fgc);
    $json       = json_encode($xml);
    $array      = json_decode($json,TRUE);
    return $fgc;//["record"];
}
function parseHeaders( $headers )
{
    $head = array();
    foreach( $headers as $k=>$v )
    {
        $t = explode( ':', $v, 2 );
        if( isset( $t[1] ) )
            $head[ trim($t[0]) ] = trim( $t[1] );
        else
        {
            $head[] = $v;
            if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                $head['reponse_code'] = intval($out[1]);
        }
    }
    return $head;
}

# Ausgabe des Ergebnisses
$x = getLoginToken($user,$pass,$hostname);
$y = getData($hostname, $x);
$z = new SimpleXMLElement($y);
# echo $z->asXML();

# Filtern der Ergebnisse nach den Angaben, die erforderlich sind
$XMLS = '';
foreach($z->ap AS $row){
    # Name trennen
    $shName = explode("-",$row->name);
    # Letzter Kontakt umrechnen
    $uptime = number_format($row->snmp_uptime/60/60/24,0,',','.');
    # IP gesetzt
    $isip = ($row->lan_ip != '') ? 1 : 0;
    # print_r($row);
    # XML
    $XMLS .= "<hardware>";
    $XMLS .= "<device_category>$row->device_category</device_category>";
    $XMLS .= "<folder>$row->folder</folder>";
    $XMLS .= "<name>$row->name</name>";
    $XMLS .= "<shortname>$shName[1]</shortname>";
    $XMLS .= "<isip>$isip</isip>";
    $XMLS .= "<lanip>$row->lan_ip</lanip>";
    $XMLS .= "<mac>$row->lan_mac</mac>";
    $XMLS .= "<uptime>$uptime</uptime>";
    $XMLS .= "<mfgr>$row->mfgr</mfgr>";
    $XMLS .= "</hardware>";
}


$XML = "<?xml version='1.0' encoding='UTF-8'?>
<network>$XMLS</network>";
header('Content-Type: application/xml; charset=utf-8');
echo $XML;