<?php
session_start();

require ($_SERVER["DOCUMENT_ROOT"] . "/lib/OAuth2/Client.php");
require ($_SERVER["DOCUMENT_ROOT"] . "/lib/OAuth2/GrantType/IGrantType.php");
require ($_SERVER["DOCUMENT_ROOT"] . "/lib/OAuth2/GrantType/AuthorizationCode.php");

//Repo to access
const OWNER = ""; // username of Repo owner
const REPO = ""; // Repo name

//GITHUB Oauth //Sorry if your such make a stop at your local Google or checkout Github help 
const CLIENT_ID = "";
const CLIENT_SECRET = "";

//URL to redirect to if Oauth is active
const HOMEURL = "";

// redirect URI for app - replace with URI for where you put this script
const REDIRECT_URI = "";

// GitHub Oauth URLs
const AUTHORIZATION_ENDPOINT = "https://github.com/login/oauth/authorize";
const TOKEN_ENDPOINT         = "https://github.com/login/oauth/access_token";

if (!isset($_SESSION["oauth_token"])) {

    // the oauth client
    $client = new OAuth2\Client(CLIENT_ID, CLIENT_SECRET);
            
    if (!isset($_GET["code"])) {
        // Send user to github oauth login
    
        // PHP-OAuth2 doesn't know about the extras for github
        $EXTRAS = Array("scope" => "repo" );
        $auth_url = $client->getAuthenticationUrl(AUTHORIZATION_ENDPOINT, REDIRECT_URI, $EXTRAS);
        header("Location: " . $auth_url);
        die; //Redirected
    } else {
        // Got the temp code, need to exchange it for a token so we can get cracking
        $params = array("code" => $_GET["code"], "redirect_uri" => REDIRECT_URI);
        $response = $client->getAccessToken(TOKEN_ENDPOINT, "authorization_code", $params);
    
        if ( $response["code"] != 200 ) {
            print "Response was " . $response["code"];
            die;
        }
    
        // got a 200 response = success?, parse the response and try to get token
        parse_str($response["result"], $info);
    
        if (array_key_exists( "access_token", $info) ) {
            $_SESSION["oauth_token"] = $info["access_token"];
            // hand the token over to GitHubClient to start doing the query
        } else {
             print "<h1> FAILURE - no token </h1>";
                $errorLog = $info;
                $errorStatus = "Failure, No Token - " . date("d-m-Y h:i:s A") . " - " . $errorLog . "\r\n\r\n";
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/error_log.txt", $errorStatus, FILE_APPEND | LOCK_EX);
                print_r($info);
        }
    }  
};

if ((isset($_SESSION["oauth_token"])) && (isset($_GET["code"]))) { 
    header(HOMEURL);  
    exit;
};
?>