<?php

require '../vendor/autoload.php';

$accessToken = $_GET['access_token'];
$subscriberNumber = $_GET['subscriber_number'];

file_put_contents('subscribers.txt', $accessToken . ',' . $subscriberNumber, FILE_APPEND);

$sms = new \Coreproc\Globe\Labs\Api\Services\Sms('eB75F4dyoGhRdcEqxbTyMXhR9BRBFKx8', '75ccbb7bcda7c34b649c9396c531e2a5c5ba5a94f4bacdca8c3c867528f477ab');

$sms->send($accessToken, $subscriberNumber, 'thanks for subscribing!', '21582328');
