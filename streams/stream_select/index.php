<?php

// Пример многопоточности (асинхронности) в PHP с помощью stream_select
// Задача распаралелить несколько запросов в несколько потоков.
// Таким образом время затраченное на получение данных по сети
// будет равно времени ответа самого долгого из запросов, а не сумме времени всех запросов.

require "./Stream.php";

$reads = [];
for ($i = 3; $i > 0; $i--) {
    $stream = stream_socket_client('tcp://example.ru:80', $errno, $errstr, 30);
    if(!$stream) {
        echo "$errstr ($errno)<br>\n";
    } else {
        fwrite($stream, "GET /?sleep=$i HTTP/1.0\r\nHost: example.ru\r\nAccept: */*\r\n\r\n");
    }

    array_push($reads, $stream);
}

$stream = new Stream($reads);
$stream->handle(function($data) {
    echo "$data\n\n";
});