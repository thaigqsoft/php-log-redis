<?php
// Composer install this package.
// https://github.com/reactphp/socket
error_reporting(E_ALL);
//error_reporting( error_reporting() & ~E_NOTICE );
// error_reporting(0);

require 'vendor/autoload.php';
include 'config.php';
ini_set('date.timezone', 'Asia/Bangkok');


$redis = new Predis\Client([
    "scheme" => "tcp",
    "host" => "$REDIS_SERVER",
    "port" => 6379,
    "persistent" => "0",
    "database" => "$DATABASE_REDIS",
    "password"=> "$REDIS_PASSWORD",
]);

function objectToArray($d)
{
  if (is_object($d)) {
    // Gets the properties of the given object
    // with get_object_vars function
    $d = get_object_vars($d);
  }

  if (is_array($d)) {
    /*
    * Return array converted to object
    * Using __FUNCTION__ (Magic constant)
    * for recursive call
    */
    return array_map(__FUNCTION__, $d);
  } else {
    // Return array
    return $d;
  }
}

$loop = React\EventLoop\Factory::create();
$factory = new React\Datagram\Factory($loop);
$factory->createServer("$IP_SERVER:$OPEN_PORT_LOG")->then(function (React\Datagram\Socket $server) {
    $server->on('message', function($message, $address, $server) {
      //  $server->send('hello ' . $address . '! echo: ' . $message, $address);

      //  echo 'client ' . $address . ': ' . $message . PHP_EOL;


        $T=time();

        $ips=explode(":",$address);
        $ip=$ips[0];

        $log_date=date("Y-m-d");
        $log_time=date("H");
        $log_time2=date("i:s");

        include 'config.php';
        $redis = new Predis\Client([
            "scheme" => "tcp",
            "host" => "$REDIS_SERVER",
            "port" => 6379,
            "persistent" => "0",
            "database" => "$DATABASE_REDIS",
            "password"=> "$REDIS_PASSWORD",
        ]);


         $message_clent=json_decode($message);
         $message_clent=objectToArray($message_clent);
         print_r($message_clent);
/*
         $redis->hmset("$ip:$log_date:$msg_data[0]:$log_time:$log_time2", array(
            "data" => "$message",
            "hostname" => "$hostname_client",
         )
        );
        */

    });
});
$loop->run();
