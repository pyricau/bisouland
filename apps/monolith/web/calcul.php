<?php

$lvl = 1;
$val = 10;

$lvlp = 20;
$valp = 4000;

$b = log($valp / $val) / (1 / $lvl - 1 / $lvlp);
$a = $val * exp($b / $lvl);
echo 'a : ' . $a . '<br />b: ' . $b;
