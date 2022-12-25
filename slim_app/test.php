<?php

$s = "Foo123/";
#echo substr($s, 0,-1) . PHP_EOL;

print_r(
    preg_match("!\/$!", $s)
);

echo PHP_EOL;