<?php
// includes/db.php
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

try {
    $factory = (new Factory)
        ->withServiceAccount(__DIR__ . '/../firebase-credentials.json')
        ->withDatabaseUri('https://al-amad-555e0-default-rtdb.europe-west1.firebasedatabase.app');

    $auth = $factory->createAuth();
    $database = $factory->createDatabase();
} catch (Exception $e) {
    die("Firebase connection failed: " . $e->getMessage());
}
?>
