<?php

function VerifyToken($token, $clientid)
{
    if (!extension_loaded('openssl')) {
        throw new Exception('The PHP openssl extension is missing.'); //, Exception::CURL_NOT_FOUND);
    }

    $tokensegs = explode('.', $token);
    $cnt = count($tokensegs);
    if( $cnt != 3) {
        $validtoken = false;
        throw new Exception('The JWT has an incorrect number of segments');
    }
    else
    {
        $validtoken = VerifySignature( $tokensegs[0], $tokensegs[1], $tokensegs[2] );
    }

    if( $validtoken == true ) {
        $decodeddata = base64decode_url( $tokensegs[1] );
        $decodeddata = trim( $decodeddata, "{}" );
        $decodeddata = str_replace( '"', '', $decodeddata );
        $tokendata = array();
        foreach ( explode( ',', $decodeddata ) as $pair ) {
            list( $key, $val ) = explode( ':', $pair, 2 );
            $tokendata[$key] = $val;
        }
        $validtoken &= VerifyTokenData( $tokendata, $clientid );
    }
    else
    {
        $tokendata = "Invalid Token Received";
    }
    LogTokenData( $tokendata, $validtoken );

    return $validtoken;
}


function VerifySignature($header, $payload, $sig)
{

    $pubkeyid = openssl_pkey_get_public( "file:///etc/ssl/certs/jwtpubkey.cer");
    $token = $header . '.' . $payload;
    $sig2verify = base64decode_url( $sig );
    $sigvalid = openssl_verify( $token, $sig2verify, $pubkeyid, 'SHA256' );
    if( $sigvalid == 1 )
    {
        return true;
    }
    elseif( $sigvalid == 0)
    {
        return false;
    }
    else
    {
        throw new DomainException( 'openssl_verify error: ' . openssl_error_string() );
    }
    return false;
}

function VerifyTokenData($tkdata, $clientid )
{
    $retval = false;

    $curtime = time();
    $tokenexp = intval( $tkdata['exp'] );
    if( $curtime < $tokenexp )
    {
        if( $clientid == $tkdata['client_id'] )
        {
            if( $tkdata['scope'] != "none" && $tkdata['scope'] != "[none]" )
            {
                $retval = true;
            }
        }
    }
    return $retval;
}

function LogTokenData($tkdata, $usetoken)
{

    $file = fopen( "/rdklogs/logs/webui.log", "a" );
    if( $file != FALSE )
    {
        $str = date("Y-m-d H:i:s");
        if( $usetoken == true )
        {
            $str = $str . " WebUI: OAUTH userId=" . $tkdata['COMCAST_EMAIL'];
            $str = $str . " scope=" . $tkdata['scope'];
            $str = $str . " expiration=" . $tkdata['exp'];
        }
        else
        {
            $str = $tkdata;
        }
        $str = $str . "\n";
        fwrite( $file, $str );
        fclose( $file );
    }
}

function base64decode_url($string)
{
	/* Need to map non-RFC-1421 characters in the URL to the proper base64 charset. */
	$data = str_replace( "-", "+", $string);
	$data = str_replace( "_", "/", $data);
	/* Decode input must be a multiple of 4 bytes so pad up with “=”. */
	$mod4 = strlen($data) % 4;

	switch ($mod4)
	{ 
		case 1: 
			 $data = $data."==="; 
			 break;
	 	case 2:
	 		 $data = $data."=="; 
	 		 break;
	 	case 3:
	 		 $data = $data."=";
	 		 break; 
	}
	 return base64_decode($data);
}

