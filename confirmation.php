<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            margin: 10px 0;
            color: #666;
        }

        .warning {
            color: red;
            font-weight: bold;
            font-size: 14px;
        }

        .code {
            font-weight: bold;
            display: block;
            font-size: 18px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Configuration Saved</h1>
        
        <?php
        // Check if 'code' parameter is present in the URL
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            echo "<p>Configuration saved with code: <span class='code'>$code</span></p>";
            echo "<p class='warning'>Please note this Code to retrieve the saved configuration in the future.</p>";
        } else {
            echo "<p>Error: Code not found.</p>";
        }
        ?>

        <button onclick="redirectToIndex()">Okay</button>
    </div>

    <script>
        function redirectToIndex() {
            // Redirect to the main index page
            window.location.href = 'index.html?loadedCode=<?php echo $code; ?>';
        }
    </script>

</body>
</html>
