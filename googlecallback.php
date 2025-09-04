<?php
session_start();
require 'db.php';
require 'vendor/autoload.php'; // Composer autoload for Google API

use Google\Client;
use Google\Service\Oauth2;

// Set up Google Client
$client = new Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/Dating_Web/google-callback.php'); // Update this to your callback URL
$client->addScope('email');
$client->addScope('profile');

if (!isset($_GET['code'])) {
    // No code, redirect to registration page
    header('Location: Registration.php');
    exit();
}

try {
    // Exchange code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        throw new Exception($token['error_description']);
    }
    $client->setAccessToken($token['access_token']);

    // Get user info
    $oauth2 = new Oauth2($client);
    $userinfo = $oauth2->userinfo->get();

    $name = $userinfo->name;
    $email = $userinfo->email;
    $google_id = $userinfo->id;

    // Connect to DB
    $conn = new mysqli("localhost", "root", "", "dating_app");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    // Check if user already exists
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // User exists, log in or redirect
        $_SESSION['email'] = $email;
        header('Location: dashboard.php'); // Update this to your post-login page
        exit();
    } else {
        // User does not exist, register user
        $default_password = password_hash($google_id, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, status) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $name, $email, $default_password);
        if ($stmt->execute()) {
            $_SESSION['email'] = $email;
            header('Location: dashboard.php'); // Update this to your post-registration page
            exit();
        } else {
            echo "Registration failed: " . $stmt->error;
        }
        $stmt->close();
    }
    $stmt_check->close();
    $conn->close();
} catch (Exception $e) {
    echo "Authentication error: " . htmlspecialchars($e->getMessage());
}
?>