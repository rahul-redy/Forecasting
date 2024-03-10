<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        // Generate a random code for the filename
    $loadedCode = isset($_POST['loadedCode']) ? $_POST['loadedCode'] : null;

        // If loaded code is not present, generate a random code
     // Check if the code is randomly generated
    $isRandomCode = isset($_POST['isRandomCode']) ? $_POST['isRandomCode'] : false;
    if (!$loadedCode) {
        $loadedCode = uniqid();
        $isRandomCode = true;
    }
    
        // Define the filename with the loaded code
    $subfolder = 'users_data';
    $filename = "$subfolder/config_$loadedCode.xml";

    
        // Check if the file already exists
    if (file_exists($filename)) {
        // If the file exists, read its content for further modifications
        $editedConfig = file_get_contents($filename);
    } else {
        // If the file does not exist, read the content of the original configuration file
        $editedConfig = file_get_contents('confHurricanes.xml');
    }


    $selectedColorbars = isset($_POST['colorbars']) ? $_POST['colorbars'] : array();
    foreach ($selectedColorbars as $colorbar) {
        $editedConfig = str_replace("<!--        <colorbar>\n<!--            <name>$colorbar</name>", "<colorbar>\n<name>$colorbar</name>", $editedConfig);
    }

     // Clear existing servers within <opendapservers> section
    preg_match('/<opendapservers>(.*?)<\/opendapservers>/s', $editedConfig, $matches);
    $opendapServersContent = $matches[1] ?? '';
 
    if (!empty($opendapServersContent)) {
        $opendapServersContent = preg_replace("/<server>.*?<\/server>/s", "", $opendapServersContent);
        $editedConfig = str_replace($matches[1], $opendapServersContent, $editedConfig);
    }
    
    $serverNames = $_POST['server_names'];
    $serverURLs = $_POST['server_urls'];


    $newServerBlocks = '';
    for ($i = 0; $i < count($serverNames); $i++) {
        $name = $serverNames[$i];
        $url = $serverURLs[$i];

        $newServerBlock = "<server>\n<name>$name</name>\n<url>$url</url>\n<grid name=\"rho\" floatType=\"float64\">lat_rho,lon_rho</grid>\n<time offset=\"1858-11-17T00:00:00.000Z\" units=\"seconds\" floatType=\"float64\">ocean_time</time>\n</server>\n";
        $newServerBlocks .= $newServerBlock;
    }


    $insertPosition = strpos($editedConfig, '</opendapservers>');

    
    $editedConfig = substr_replace($editedConfig, $newServerBlocks, $insertPosition, 0);
    


    if (!file_exists($subfolder) && !is_dir($subfolder)) {
        mkdir($subfolder);
    }

    file_put_contents($filename, $editedConfig);

    
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