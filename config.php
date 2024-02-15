<?php

require_once 'vendor/autoload.php';

session_start();

// Google API configuration
$clientID = '478171995584-93bc5gleg5btak4o3ju0nuo1egbr21au.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/portfoliohub/store_google_data.php';

// Create Google_Client instance
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$database = "portfoliohub";

// Connect to the database using MySQLi (object-oriented style)
$conn = new mysqli($hostname, $username, $password, $database);

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


