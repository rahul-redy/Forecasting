<?php

// Check if the loadedCode parameter is present in the URL
if (isset($_GET['loadedCode'])) {
    $loadedCode = $_GET['loadedCode'];
    echo "Loaded Code: $loadedCode"; // Debug statement to check the loadedCode

    // Define the filename based on the loaded code
    $subfolder = 'users_data';
    $filename = "$subfolder/config_$loadedCode.xml";
    echo "Filename: $filename"; // Debug statement to check the filename

    // Check if the configuration file with the loaded code exists
    if (file_exists($filename)) {
        // Read the content of the configuration file
        $xmlContent = file_get_contents($filename);
        $xml = simplexml_load_string($xmlContent);

        $existingServers = array();

        // Specify the XPath more precisely to target servers within <opendapservers>
        foreach ($xml->xpath('//opendapservers/server') as $index => $server) {
            $serverName = (string)$server->name;
            $serverURL = (string)$server->url;

            // Include variables for each server
            $variables = array(
                'ubar_eastward, vbar_northward',
                'u_sur_eastward, v_sur_northward',
                'zeta', 'Hwave',
                'temp_sur', 'salt_sur'
            );
            $existingServers[] = array("id" => $index, "name" => $serverName, "url" => $serverURL, "variables" => $variables);
        }

        header('Content-Type: application/json');
        echo json_encode($existingServers);
        exit(); // Stop further execution
    } else {
        // Debug statement to check if the file exists
        header('HTTP/1.1 404 Not Found');
        exit();
    }
} else {
    // If loadedCode is not present or the file doesn't exist, proceed with the default behavior
    $configFile = 'confHurricanes.xml';

    if (file_exists($configFile)) {
        $xmlContent = file_get_contents($configFile);
        $xml = simplexml_load_string($xmlContent);

        $existingServers = array();

        // Specify the XPath more precisely to target servers within <opendapservers>
        foreach ($xml->xpath('//opendapservers/server') as $index => $server) {
            $serverName = (string)$server->name;
            $serverURL = (string)$server->url;

            // Include variables for each server
            $variables = array(
                'ubar_eastward, vbar_northward',
                'u_sur_eastward, v_sur_northward',
                'zeta', 'Hwave',
                'temp_sur', 'salt_sur'
            );
            $existingServers[] = array("id" => $index, "name" => $serverName, "url" => $serverURL, "variables" => $variables);
        }

        header('Content-Type: application/json');
        echo json_encode($existingServers);
    } else {
        header('HTTP/1.1 404 Not Found');
    }
}
?>
