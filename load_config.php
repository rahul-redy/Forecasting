<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['code'])) {
    $code = $_GET['code'];
    $subfolder = 'users_data';
    $filename = "$subfolder/config_$code.xml";

    // Check if the file exists
    if (file_exists($filename)) {
        // Read the content of the configuration file
        $configContent = file_get_contents($filename);


        // Output the content to be read by Brython or other JavaScript
        echo "<script>var loadedConfigContent = `" . addslashes($configContent) . "`;</script>";

        echo "<script>console.log('Loaded Config Content:', `" . addslashes($configContent) . "`);</script>";


        // Redirect to the main index page with the loaded code as a parameter
        echo "<script>window.location.href = 'index.html?loadedCode=$code';</script>";
        exit();
    } else {
        // Code doesn't match any file, handle accordingly
        echo "<script>alert('Invalid code or configuration not found.'); window.location.href = 'index.html';</script>";
        exit();
    }
} else {
    // Redirect to the main index page if not a valid request
    echo "<script>window.location.href = 'index.html';</script>";
    exit();
}
?>
