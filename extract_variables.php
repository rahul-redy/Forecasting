<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (isset($_GET['url'])) {
    $netcdfUrl = $_GET['url'];
    
    // Fetch NetCDF file
    $netcdfContent = file_get_contents($netcdfUrl);

    if ($netcdfContent === false) {
        logError('Failed to fetch NetCDF content');
        sendJsonResponse(['error' => 'Failed to fetch NetCDF content']);
    }

    //  extract variables
    preg_match_all('/(\w+)(\[.*?\])?;/m', $netcdfContent, $matches, PREG_SET_ORDER);

    $variablesNetCDF = [];
    foreach ($matches as $match) {
        // Check if the variable has an array
        if (isset($match[2])) {
            $variablesNetCDF[$match[1]] = trim($match[2], "[]");
        }
    }

    // Read the .dat file
    $datFile = 'variables_for_graphs.dat'; 
    $datContent = file_get_contents($datFile);
    $lines = explode("\n", $datContent);
    $variablesDat = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            // Extract variables 
            $vars = preg_split('/\t+/', $line);

            if (isset($vars[2])) {
                $variablesDat[] = [
                    'variables' => array_map('trim', explode(',', $vars[0])),
                    'long_name' => trim($vars[1]),
                    'short_name' => isset($vars[2]) ? trim($vars[2]) : null,
                ];
            }
        }
    }

    // Find common variables
    $commonVariables = [];
    foreach ($variablesDat as $datVars) {
        $intersection = array_intersect($datVars['variables'], array_keys($variablesNetCDF));
        if (!empty($intersection)) {
            $commonVariables[] = [
                'variables' => array_values($intersection),
                'long_name' => $datVars['long_name'],
                'short_name' => $datVars['short_name'],
            ];
        }
    }

    // Return common variables as JSON
    sendJsonResponse($commonVariables);
} else {
    // If 'url' parameter is not set, return an empty JSON array
    sendJsonResponse([]);
}

// Function to log errors to a file
function logError($error) {
    $logFile = 'error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $error\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Function to send JSON response
function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
