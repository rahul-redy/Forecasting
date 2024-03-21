<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //sleep(5);
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
    // Handle server deletion
    if (isset($_POST['deleteServer'])) {
        $deletedServers = json_decode($_POST['deleteServer'], true); // Decode JSON string into array
        foreach ($deletedServers as $serverToDelete) {
            $editedConfig = preg_replace("/<server>\s*<name>$serverToDelete<\/name>.*?<\/server>/s", "", $editedConfig);
        }
    }

//--------------------------------------------------------------------------

    // Handle adding new servers
    $serverNames = $_POST['server_names'];
    $serverURLs = $_POST['server_urls'];

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

//--------------------------------------------------------------------------

    // Clear existing layers within <layers> section
    preg_match('/<layers>(.*?)<\/layers>/s', $editedConfig, $matches);
    $layersContent = $matches[1] ?? '';

    if (!empty($layersContent)) {
        $layersContent = preg_replace("/<layer>.*?<\/layer>/s", "", $layersContent);
        $editedConfig = str_replace($matches[1], $layersContent, $editedConfig);
    }

    $serverNames = $_POST['server_names'];


    $serverVariables = $_POST['server_variables']; // Change to 'server_variables'


    $newLayerBlocks = '';
    foreach ($serverNames as $name) {

            $variablesArray = json_decode($serverVariables[$name], true);

            if (is_array($variablesArray)) {

            foreach ($variablesArray as $variable) { // Change to 'serverVariables'
                $newLayerBlock = "<layer>\n<server>$name</server>\n<servertype>dap</servertype>\n";

                
                if ($variable == 'Risk') {
                // Configuration for Risk variable
                    $newLayerBlock .= "<layertype>dynscatter</layertype>\n<gridtype>LatLon</gridtype>\n<name>$variable</name>\n<varthreshold>12.0</varthreshold>\n<varscale>12.0</varscale>\n<longname>Damage level</longname>\n<shortname>Damage.</shortname>\n<units>m/s</units>\n<colorbar>risk</colorbar>\n<visible>False</visible>\n<transparent>True</transparent>\n";
                } else {
                // Configuration for other variables
                    $newLayerBlock .= "<layertype>dynmap</layertype>\n<gridtype>rho</gridtype>\n<name>$variable</name>\n";

                // Variable-specific configurations
                    switch ($variable) {
                        case 'salt_sur':
                            $newLayerBlock .= "<varthreshold>1110.0</varthreshold>\n<varscale>1.0</varscale>\n<longname>Surface salinity</longname>\n<shortname>SSS</shortname>\n<units></units>\n<colorbar>salinity</colorbar>\n";
                            break;

                        case 'temp_sur':
                            $newLayerBlock .= "<varthreshold>1110.0</varthreshold>\n<varscale>1.0</varscale>\n<longname>Surface temperature</longname>\n<shortname>SST</shortname>\n<units>°C</units>\n<colorbar>temperature</colorbar>\n";
                            break;

                        case 'Hwave':
                            $newLayerBlock .= "<varthresholdmin>-1.89</varthresholdmin>\n<varthresholdmax>100</varthresholdmax>\n<varscale>1.0</varscale>\n<longname>Wave amplitude</longname>\n<shortname>Hm0</shortname>\n<units>m</units>\n<colorbar>Hm0</colorbar>\n";
                            break;

                        case 'zeta':
                            $newLayerBlock .= "<varthresholdmin>-1.89</varthresholdmin>\n<varthresholdmax>100</varthresholdmax>\n<varscale>1.0</varscale>\n<longname>Water level</longname>\n<shortname>Water lvl.</shortname>\n<units>m</units>\n<colorbar>zeta</colorbar>\n";
                            break;

                        case 'u_sur_eastward,v_sur_northward':
                            $newLayerBlock .= "<varthreshold>12.0</varthreshold>\n<varscale>12.0</varscale>\n<longname>Surface currents</longname>\n<shortname>Srfc curr</shortname>\n<units>m/s</units>\n<colorbar>velocity</colorbar>\n";
                            break;
                    
                        case 'ubar_eastward, vbar_northward':
                            $newLayerBlock .= "<varthreshold>12.0</varthreshold>\n<varscale>12.0</varscale>\n<longname>Barotropic velocity</longname>\n<shortname>Barotrop. vel</shortname>\n<units>m/s</units>\n<colorbar>velocity</colorbar>\n";
                            break;
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
