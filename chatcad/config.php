<?php

$user = 'root';
$pass = 'Password1';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$isDocker = getenv('IS_DOCKER') === 'true';

// Set the DSN based on the environment
if ($isDocker) {
    $dsn = 'mysql:host=host.docker.internal;dbname=chatcad';
} else {
    $dsn = 'mysql:host=localhost;dbname=chatcad';
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

?>