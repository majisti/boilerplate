<?php

/**
 * @param array $hosts
 * @param $currentHost
 * @return bool
 */
$isCurrentHostAllowed = function (array $hosts, $currentHost) {
    $allowed = false;
    reset($hosts);

    while( ($ip = current($hosts)) && !$allowed ) {
        if( preg_match("/^{$ip}$/", $currentHost) ) {
            $allowed = true;
        }

        next($hosts);
    }

    return $allowed;
};

/* defining network ips with range */
//local, network, kingpin, srosato
$hosts = array(
    '127.0.0.1',
    '10..*..*..*', //local network, usually used by virtual machines
    '192.168..*..*',
    '172.17.0..*', //docker
    '::1',
    'modemcable089.165-177-173.mc.videotron.ca', //Majisti's Office
);

$allowed = false;

$allowed = $isCurrentHostAllowed($hosts, @$_SERVER['REMOTE_ADDR']);

if( !$allowed && isset($_SERVER['REMOTE_ADDR'])) {
    $allowed = $isCurrentHostAllowed($hosts, gethostbyaddr($_SERVER['REMOTE_ADDR']));
}

if ( !$allowed ) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}
unset($allowed, $isCurrentHostAllowed);
