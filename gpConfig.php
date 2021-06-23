<?php
session_start();

//Include Google client library 
include_once 'src/Google_Client.php';
include_once 'src/contrib/Google_Oauth2Service.php';

/*
 * Configuration and setup Google API
 */
$clientId = 'id'; //Google client ID
$clientSecret = 'secret'; //Google client secret
$redirectURL = 'https://orloov.com/'; //Callback URL

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('login_with_google_using_php');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>
