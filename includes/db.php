<?php
// includes/db.php
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

try {
    // Check for environment variable first (Production), then fallback to local file (Development)
    $envCredentials = getenv('FIREBASE_CREDENTIALS');
    
    if ($envCredentials) {
        // Assume the environment variable contains the JSON string
        $serviceAccount = json_decode($envCredentials, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in FIREBASE_CREDENTIALS environment variable.");
        }
    } else {
        $serviceAccount = __DIR__ . '/../firebase-credentials.json';
        if (!file_exists($serviceAccount)) {
            throw new Exception("Firebase credentials not found (tried 'FIREBASE_CREDENTIALS' env var and 'firebase-credentials.json' file).");
        }
    }

    $factory = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://al-amad-555e0-default-rtdb.europe-west1.firebasedatabase.app');

    $auth = $factory->createAuth();
    $database = $factory->createDatabase();
} catch (Exception $e) {
    die("Firebase connection failed: " . $e->getMessage());
}
?>
