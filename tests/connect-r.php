<?php

//@fsockopen(ssl://pop.gmx.com:995, 0, '', 10.5)

$proto = 'ssl';                 //tcp|ssl|sslv2|sslv3|tls
$host  = 'pop.gmx.com'; //imap.gmx.com
$port  = '995';

$timeout = 10.0; //in seconds

if( function_exists('fsockopen') ){

    $intErrno = 0; $strError = '';

    $socket =  fsockopen($proto. "://" . $host .":". $port, $intErrno, $strError, $timeout);

    if( $socket )
        echo "CONNECTED TO {$host};";
    else
        echo "COULD NOT CONNECT TO  {$host};";

}
else echo "fsockopen SUPPORT MISSING!";