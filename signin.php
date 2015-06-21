<?php

include_once('init.php');
set_include_path(get_include_path() . PATH_SEPARATOR . EXROOT . 'dev/libraries/google-api-php-client/src');
require_once('Google/autoload.php');

$client_id = '244942183777-fi8bp76m3in1rueqjnkghp152d4hfpga.apps.googleusercontent.com';
$client_secret = 'f6KsTE5nK_TKzE7HtqKeA3HY';
$redirect_uri = 'signin.php';

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setScopes('email');

function getUserFromToken($token, $client) {
  $ticket = $client->verifyIdToken($token);
  if ($ticket) {
    $data = $ticket->getAttributes();
    return $data['payload']['sub']; // user ID
  }
  return false;
}

$token = $_POST['idtoken'];
echo getUserFromToken($token, $client);
?>
