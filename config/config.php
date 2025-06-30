<?php
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        die("Environment file not found: $filePath");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse the line
        $keyValuePair = explode('=', $line, 2);
        if (count($keyValuePair) === 2) {
            $key = trim($keyValuePair[0]);
            $value = trim($keyValuePair[1]);

            // Remove quotes if present
            $value = trim($value, '"\'');

            // Set the variable in the environment
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}


?>