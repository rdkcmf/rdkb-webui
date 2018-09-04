<?php

function VerifyToken($token, $clientid)
{
    if (!extension_loaded('openssl')) {
        throw new Exception('The PHP openssl extension is missing.'); //, Exception::CURL_NOT_FOUND);
    }

    $tokensegs = explode('.', $token);
    if (count($tokensegs) != 3) {
        throw new Exception('The JWT has an incorrect number of segments');
    }
    $validtoken = VerifySignature( $tokensegs[0], $tokensegs[1], $tokensegs[2] );
//    var_dump($validsig);

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
    LogTokenData( $tokendata );

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
            $retval = true;
        }
    }
    return $retval;
}

function LogTokenData($tkdata)
{

    $file = fopen( "/rdklogs/logs/webui.log", "a" );
    if( $file != FALSE )
    {
        $str = date("Y-m-d H:i:s");
        $str = $str . " WebUI: OAUTH userId=" . $tkdata['COMCAST_EMAIL'];
        $str = $str . " scope=" . $tkdata['scope'];
        $str = $str . " expiration=" . $tkdata['exp'];
        $str = $str . "\n";
        fwrite( $file, $str );
        fclose( $file );
    }
}

 /***************************************************************************
 *   Copyright (C) 2006-2009 by Anton E. Lebedevich                        *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 *    UrlSaveBase64* functions borrowed from comments on                   *
 *    http://www.php.net/manual/en/function.base64-encode.php              *
 *    by massimo dot scamarcia at gmail dot com                            *
 *                                                                         *
 ***************************************************************************/

function base64decode_url($string)
{
    $data = str_replace( array('-', '_'),
                         array('+', '/'),
                         $string);

    $mod4 = strlen($data) % 4;

    if ($mod4) {
        $data .= substr('====', $mod4);
    }

    return base64_decode($data);
}

