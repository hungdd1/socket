<?php

use React\EventLoop\Factory;
use React\SocketClient\TcpConnector;
use React\SocketClient\DnsConnector;
use React\SocketClient\SecureConnector;
use React\Stream\Stream;

require __DIR__ . '/../vendor/autoload.php';

$loop = Factory::create();

$factory = new \React\Dns\Resolver\Factory();
$resolver = $factory->create('8.8.8.8', $loop);

$tcp = new TcpConnector($loop);
$dns = new DnsConnector($tcp, $resolver);
$tls = new SecureConnector($dns, $loop);

$tls->create('www.google.com', 443)->then(function (Stream $stream) {
    $stream->on('data', function ($data) {
        echo $data;
    });
    $stream->on('close', function () {
        echo '[CLOSED]' . PHP_EOL;
    });

    $stream->write("GET / HTTP/1.0\r\nHost: www.google.com\r\n\r\n");
}, 'printf');

$loop->run();
