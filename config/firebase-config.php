<?php
// Firebase Configuration
// This file contains the Firebase configuration that will be used across the application

$firebaseConfig = [
    'apiKey' => "AIzaSyCIjDPMvgKVTpleUCYWtMIu-K6bW1gHJZY",
    'authDomain' => "greentrade-project.firebaseapp.com",
    'projectId' => "greentrade-project",
    'storageBucket' => "greentrade-project.firebasestorage.app",
    'messagingSenderId' => "582047266659",
    'appId' => "1:582047266659:web:47054d9178fbd66f0d8556",
    'measurementId' => "G-M2FMJ35F4K"
];

// Convert PHP array to JavaScript object for use in frontend
function getFirebaseConfigJS() {
    global $firebaseConfig;
    return json_encode($firebaseConfig, JSON_PRETTY_PRINT);
}
?>
