<?php

if ($argc != 2) exit(1);

$passwd = $argv[1];
$hash = password_hash($passwd, PASSWORD_BCRYPT);
#$verify = password_verify($passwd, $hash);

echo("Passwd: " . $passwd . "\n");
echo("Hash: " . $hash . "\n");