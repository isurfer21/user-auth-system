<?php

$env = parse_ini_file('.env');
// echo $env["VERSION"];

require 'vendor/autoload.php';

require 'lib/uas/handshake.php';
require 'lib/uas/user-account.php';

use flight\Engine;

$app = new Engine();

$handshake = new Handshake();
$app->route('GET|POST /', array($handshake, 'hello'));

$userAccount = new UserAccount($env);
$app->route('POST /login', array($userAccount, 'login'));
$app->route('POST /register', array($userAccount, 'register'));
$app->route('POST /forgot-password', array($userAccount, 'forgotPassword'));

$app->start();