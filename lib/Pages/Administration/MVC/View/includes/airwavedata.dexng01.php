<?PHP
/*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
*/
    $hostname   = "cau-airwave.service.rhs.zz";
    $user       = "apiview";
    $pass       = "F9h6hy4U3O2D80t81BTRN3G9eiD5u6!";
    $url        = "https://" . $hostname . "/alerts.xml";
    $url        = "https://" . $hostname . "/ap_list.xml?ap_folder_id=8";
    $retval     = getLoginToken($user,$pass,$hostname);
    $retxml     = getData($hostname,$retval);


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
        $url        = "https://" . $hostname . "/ap_list.xml?ap_folder_id=8";
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

    $x = getLoginToken($user,$pass,$hostname);
    $y = getData($hostname, $x);
    $z = new SimpleXMLElement($y);
    echo $z->asXML();