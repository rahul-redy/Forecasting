<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
</head>
<body>

    <h1>Configuration Saved</h1>
    
    <?php
    // Check if 'code' parameter is present in the URL
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        echo "<p>Configuration saved with code: $code</p>";
    } else {
        echo "<p>Error: Code not found.</p>";
    }
    ?>

    <button onclick="redirectToIndex()">Okay</button>

    <script>
        function redirectToIndex() {
            // Redirect to the main index page
            window.location.href = 'index.html?loadedCode=<?php echo $code; ?>';
        }
    </script>

</body>
</html>
