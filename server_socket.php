<?php
// Composer install this package.
// https://github.com/reactphp/socket
//error_reporting(E_ALL);
//error_reporting( error_reporting() & ~E_NOTICE );
// error_reporting(0);

require 'vendor/autoload.php';

ini_set('date.timezone', 'Asia/Bangkok');


$redis = new Predis\Client([
    "scheme" => "tcp",
    "host" => "127.0.0.1",
    "port" => 6379,
    "persistent" => "0"
]);

$loop = React\EventLoop\Factory::create();
$factory = new React\Datagram\Factory($loop);
$factory->createServer('192.168.104.8:9999')->then(function (React\Datagram\Socket $server) {
    $server->on('message', function($message, $address, $server) {
      //  $server->send('hello ' . $address . '! echo: ' . $message, $address);

      //  echo 'client ' . $address . ': ' . $message . PHP_EOL;


        $T=time();

        $ips=explode(":",$address);
        $ip=$ips[0];

        $log_date=date("Y-m-d");
        $log_time=date("H");
        $log_time2=date("i:s");


        //แตก array  ของค่าที่ LOG ส่งมา
        //$data_gps_tracker_send=explode(",",$data);

        $redis = new Predis\Client([
            "scheme" => "tcp",
            "host" => "127.0.0.1",
            "port" => 6379,
            "persistent" => "0"
        ]);
        echo $message."\r\n";

        $redis->hmset("$ip:$log_date:$log_time:$log_time2", array(
            "data" => "$message",
         )
        );

    });
});
$loop->run();
