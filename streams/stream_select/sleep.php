<?php

$time = (int) $_GET['sleep'];
sleep($time);

echo "<br><b>Sleep time: $time</b><br>";