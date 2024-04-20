<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Generate a random code for the filename
    $loadedCode = isset($_POST['loadedCode']) ? $_POST['loadedCode'] : null;

    // If loaded code is not present, generate a random code
    $isRandomCode = isset($_POST['isRandomCode']) ? $_POST['isRandomCode'] : false;

    if (!$loadedCode) {
        $loadedCode = uniqid();
        $isRandomCode = true;
    }

    // Define the filename with the loaded code
    $subfolder = 'users_data';
    $configFile = "$subfolder/config_$loadedCode.xml";

    // Check if the file already exists
    if (file_exists($configFile)) {
        // If the file exists, read its content for further modifications
        $editedConfig = file_get_contents($configFile);
    } else {
        // If the file does not exist, read the content of the original configuration file
        $editedConfig = file_get_contents('confHurricanes.xml');
    }

    // Handle server deletion
    if (isset($_POST['deleteServer'])) {
        $deletedServers = json_decode($_POST['deleteServer'], true); // Decode JSON string into array
        foreach ($deletedServers as $serverToDelete) {
            $editedConfig = preg_replace("/<server>\s*<name>$serverToDelete<\/name>.*?<\/server>/s", "", $editedConfig);
        }
    }

    // Handle adding new servers
    $serverNames = $_POST['server_names'];
    $serverURLs = $_POST['server_urls'];
    $serverVariables = $_POST['server_variables'];

    $newServerBlocks = '';
    for ($i = 0; $i < count($serverNames); $i++) {
        // Check if the server already exists in the configuration, if not, add it
        if (strpos($editedConfig, "<name>{$serverNames[$i]}</name>") === false) {
            $newServerBlock = "<server>\n<name>{$serverNames[$i]}</name>\n<url>{$serverURLs[$i]}</url>\n<grid name=\"rho\" floatType=\"float64\">lat_rho,lon_rho</grid>\n<time offset=\"1858-11-17T00:00:00.000Z\" units=\"seconds\" floatType=\"float64\">ocean_time</time>\n</server>\n";
            $newServerBlocks .= $newServerBlock;
        }
    }

    // Insert new server blocks into the configuration
    $insertPosition = strpos($editedConfig, '</opendapservers>');
    $editedConfig = substr_replace($editedConfig, $newServerBlocks, $insertPosition, 0);

    // Clear existing layers within <layers> section
    preg_match('/<layers>(.*?)<\/layers>/s', $editedConfig, $matches);
    $layersContent = $matches[1] ?? '';

    if (!empty($layersContent)) {
        $layersContent = preg_replace("/<layer>.*?<\/layer>/s", "", $layersContent);
        $editedConfig = str_replace($matches[1], $layersContent, $editedConfig);
    }

    $newLayerBlocks = '';
    foreach ($serverNames as $name) {
        $variablesArray = json_decode($serverVariables[$name], true);

        if (is_array($variablesArray)) {
            foreach ($variablesArray as $variable) {
                $variableProperties = $_POST["variable_properties"][$name][$variable] ?? "{}";
                $variableProperties = json_decode($variableProperties, true);

                $newLayerBlock = "<layer>\n<server>$name</server>\n<servertype>dap</servertype>\n<layertype>{$variableProperties['layertype']}</layertype>\n<gridtype>rho</gridtype>\n<name>$variable</name>\n";

                foreach ($variableProperties as $propKey => $propValue) {
                    if ($propKey !== 'layertype') {
                        $newLayerBlock .= "<$propKey>$propValue</$propKey>\n";
                    }
                }

                $newLayerBlock .= "<visible>False</visible>\n<transparent>True</transparent>\n</layer>\n";
                $newLayerBlocks .= $newLayerBlock;
            }
        }
    }

    // Add the new layer blocks to the configuration
    $insertPosition = strpos($editedConfig, '</layers>');
    $editedConfig = substr_replace($editedConfig, $newLayerBlocks, $insertPosition, 0);

    // Save the modified configuration back to the file
    if (!file_exists($subfolder) && !is_dir($subfolder)) {
        mkdir($subfolder);
    }

    file_put_contents($configFile, $editedConfig);

    // Redirect the user to the main index page
    if (!$isRandomCode) {
        header("Location: index.html?loadedCode=$loadedCode");
        exit();
    } else {
        // If loaded code is not present, redirect to the confirmation page
        header("Location: confirmation.php?code=$loadedCode");
        exit();
    }
} else {
    // Redirect to the main index page if not a POST request
    header("Location: index.html");
    exit();
}
?>
