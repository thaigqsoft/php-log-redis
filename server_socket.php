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
        $msg_data=explode("@@",$message);
        echo $msg_data[1]."\r\n";

        $redis->hmset("$ip:$log_date:$msg_data[0]:$log_time:$log_time2", array(
            "data" => "$message",
         )
        );

    });
});
$loop->run();
