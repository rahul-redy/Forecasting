<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>

    <style>
        /* Customize text input box */
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            background-image: url('cloud.jpg'); /* Set background image */
            background-size: cover; /* Cover the entire background */
            background-position: center; /* Center the background image */
            background-repeat: repeat-y; /* Do not repeat the background image */
            font-family: 'Arial', sans-serif; /* Specify font-family */
            color: #333; /* Set text color */x
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #333; /* Adjust border thickness */
            border-radius: 10px; /* Round corners */
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.7); /* Set background color with 30% opacity */
            
        }
        button {
            background-color: #0077cc; /* Blue background color */
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 50%; /* Make it circular */
            cursor: pointer;
            margin-left: 20px; 
        }
        

        /* Add any additional styles as needed */
        /* Add this to your existing styles or modify as needed */

        .server-input {
    position: relative; /* Add relative positioning to the container */
    border: 2px solid black; /* Set border color to black and thickness to 2px */
    border-radius: 10px; /* Set border-radius for curved edges */
    padding: 10px; /* Add padding for spacing */
    width: 60%;
    margin-bottom: 20px; /* Adjust the margin between server inputs */
}

.remove-server {
    position: absolute; /* Set absolute positioning for the X symbol */
    top: 5px; /* Adjust the top position */
    right: 5px; /* Adjust the right position */
    cursor: pointer;
    border-radius: 90%;
}



.server-input label,
.server-input input {
    display: block;
    margin-bottom: 10px; /* Adjust margin for spacing */
}

.variables-container {
    position: relative;
    border: 2px solid black;
    border-radius: 10px;
    padding: 10px;
    margin-top: 10px;
    max-height: 200px; /* Set a maximum height for the container */
    overflow-y: auto; /* Enable vertical scrolling if content exceeds the maximum height */
    display: flex;
    flex-wrap: wrap;
}

.remove-server-variables {
    position: absolute; /* Set absolute positioning for the X symbol */
    top: 5px; /* Adjust the top position */
    right: 5px; /* Adjust the right position */
    cursor: pointer;
}

/* Set text color to white for server name and server URL */
.server-input label {
    color: black;
}


/* Set the background color of the text inputs to white */
.server-input input {
    background-color: white;
    border: 1px solid black; /* Set border color to black and thickness to 1px */
    border-radius: 10px; /* Set border-radius for curved edges */
    padding: 10px; /* Add padding for spacing */
    width: calc(100% - 20px); /* Adjust width for the input fields */
    box-sizing: border-box; /* Include padding in the width calculation */
}

.variables-container label {
    display: flex;
    align-items: center;
    margin-right: 20px; /* Adjust margin between checkboxes and labels */
    margin-bottom: 5px;
}

.variables-container input[type="checkbox"] {
    margin-right: 5px; /* Add margin to separate checkboxes from labels */
}

/* Set text color to black for server name, server URL, and variables checkboxes */
.server-input label,
.variables-container label {
    color: black;
}


    </style>
</head>
<body>

<!-- Your settings form goes here -->




<!-- commented here

<form action="save_config.php" method="post">
    <?php
    // Load the content of confHurricanes.xml for editing
    //$xmlContent = file_get_contents('confHurricanes.xml'); commented here
    ?>
    <h1>Edit Configuration</h1> 
    <textarea name="configContent" rows="10" cols="50"><?php //commented here. echo htmlspecialchars($xmlContent); ?></textarea>
    <br>
    <button type="submit">Save</button>
</form>
-->

<!-- Your settings form goes here -->

<!-- Your settings form goes here -->

<!-- Your settings form goes here -->

<!-- Your settings form goes here -->

<!-- Your settings form goes here -->

<form action="save_config.php" method="post" onsubmit="return inspectAndSubmit(event)">

    <?php
    // Load the content of confHurricanes.xml for editing
    $xmlContent = file_get_contents('confHurricanes.xml');
    ?>
    <input type="hidden" name="loadedCode" value="<?php echo htmlspecialchars($_GET['loadedCode'] ?? ''); ?>">
    <h1>Configuration</h1>

    <!-- Servers -->
    <h2>Edit Servers:</h2>
    <div id="servers-container">
        <?php
        // Assuming you have an array of servers from your XML content
        $servers = [];

        foreach ($servers as $server) {
            echo '<div class="server-input">';
            echo '<label> Server Name: <input type="text" name="server_names[]" value="' . htmlspecialchars($server['name']) . '"></label>';
            echo '<label> URL: <input type="text" name="server_urls[]" onchange="fetchServerVariables(this)" value="' . htmlspecialchars($server['url']) . '"></label>';
            // Display checkboxes for variables within a container
            // Inside the server loop
           
            echo '<div class="variables-container">';
             // Add heading for variables

            $selectedVariables = $server['variables'] ?? array();  ///changed here

            $allVariables = array(
                'ubar_eastward, vbar_northward',
                'u_sur_eastward, v_sur_northward',
                'zeta', 'Hwave',
                'temp_sur', 'salt_sur'
            );
        
        
            foreach ($allVariables as $variable) {
                $isChecked = in_array($variable, $selectedVariables);
                echo '<label><input type="checkbox" name="variables[' . htmlspecialchars($server['name']) . '][]" value="' . $variable . '" ' . ($isChecked ? 'checked' : '') . '> ' . $variable . '</label>';
            }
            echo '</div>';

            echo '<span class="remove-server" onclick="removeServer(this)">X</span>';
            echo '</div>';
        }

        ?>
    </div>
    <span id="add-server" onclick="addServer()">+ Add Server</span>

    <button type="submit" onclick="inspectFormData()">Save</button>

</form>

<div id="variables-container">
    <!--<h2>Dataset Variablezs:</h2>-->
    <!-- Display extracted variables here -->
</div>

<script>
    function addServer() {
    console.log('Adding a new server...');
    var container = document.getElementById('servers-container');
    var newServerInput = document.createElement('div');
    newServerInput.className = 'server-input';

    // Initialize variables for the new server
    var serverNameInput = '<label>Server Name: <input type="text" name="server_names[]" value=""></label>';
    var serverUrlInput = '<label>URL: <input type="text" name="server_urls[]" onchange="fetchServerVariables(this)" value=""></label>';
    
    var variablesContainer = '<div class="variables-container" id="variables-container-new"></div>'; // Container for variables

    // Add server inputs and variables to the new server div
    newServerInput.innerHTML = serverNameInput + serverUrlInput + variablesContainer +
        '<span class="remove-server" onclick="removeServer(this)">X</span>';

    container.appendChild(newServerInput);
}




async function fetchServerVariables(input) {
    var serverUrl = input.value.trim();

    if (serverUrl === '') {
        console.log('Server URL is empty.');
        return;
    }

    // Convert .dods to .dds for fetching variables
    var convertedUrlForFetch = serverUrl.replace(/\.dods$/, '.dds');

    try {
        console.log('Fetching variables for URL:', convertedUrlForFetch);
        var response = await fetch(`extract_variables.php?url=${encodeURIComponent(convertedUrlForFetch)}`);

        if (!response.ok) {
            throw new Error(`Failed to fetch variables. Status: ${response.status} ${response.statusText}`);
        }

        var contentType = response.headers.get('content-type');
        if (contentType && contentType.indexOf('application/json') !== -1) {
            var variables = await response.json();
            console.log('Fetched variables:', variables);

            // Convert .dds back to .dods for displaying checkboxes
            var convertedUrlForCheckboxes = convertedUrlForFetch.replace(/\.dds$/, '.dods');
            updateVariablesContainer(convertedUrlForCheckboxes, variables);
        } else {
            throw new Error('Invalid content type in response. Expected JSON.');
        }
    } catch (error) {
        console.error('Error fetching server variables:', error.message);
    }
}


function updateVariablesContainer(serverUrl, variables) {
    var serverContainers = document.getElementsByClassName('server-input');
    for (var i = 0; i < serverContainers.length; i++) {
        var urlInput = serverContainers[i].querySelector('input[name^="server_urls"]');
        if (urlInput && urlInput.value.trim() === serverUrl) {
            var variablesContainer = serverContainers[i].querySelector('.variables-container');
            variablesContainer.innerHTML = ''; // Clear existing checkboxes

            if (Array.isArray(variables)) {
                variables.forEach(function (variableSet, index) {
                    var checkbox = createCheckbox(serverUrl, variableSet.variables, index);
                    variablesContainer.appendChild(checkbox);
                });
            } else {
                console.error('Invalid variables format:', variables);
            }

            break;
        }
    }
}


function createCheckbox(serverUrl, variableNames, index) {
    var checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.name = `variables[${serverUrl}][${index}][]`;
    checkbox.value = variableNames.join(','); // Combine variable names
    checkbox.id = `${serverUrl}_index_${index}`; // Use the index as part of the ID

    var label = document.createElement('label');
    label.appendChild(checkbox);
    label.appendChild(document.createTextNode(` ${variableNames.join(', ')}`));

    return label;
 }


// Initialize the array to store deleted servers
var deletedServers = [];

function removeServer(element) {
    var container = document.getElementById('servers-container');
    var serverInput = element.parentNode;
    container.removeChild(serverInput);

    // Get the server name
    var serverName = serverInput.querySelector('input[name^="server_names"]').value;
    // Add the server name to the array of deleted servers
    deletedServers.push(serverName);
}

// Modify the form submission logic
// Modify the form submission logic
function inspectAndSubmit(event) {
    //event.preventDefault(); // Prevent the default form submission behavior

    // Set the deleteServer parameter in the form for all deleted servers
    var form = document.querySelector('form');
     
    // Create a hidden input field to store the deleted servers
    var deleteServerInput = document.createElement('input');
    deleteServerInput.type = 'hidden';
    deleteServerInput.name = 'deleteServer';
    deleteServerInput.value = JSON.stringify(deletedServers);
    form.appendChild(deleteServerInput);

    // Include selected variables for each server
    var serverInputs = document.querySelectorAll('.server-input');
    serverInputs.forEach(function(serverInput, index) {
        var serverName = serverInput.querySelector('input[name^="server_names"]').value;
        var selectedVariables = [];
        var checkboxes = serverInput.querySelectorAll('input[type="checkbox"]:checked');
        checkboxes.forEach(function(checkbox) {
            selectedVariables.push(checkbox.value);
        });
        var variablesInput = document.createElement('input');
        variablesInput.type = 'hidden';
        variablesInput.name = 'server_variables[' + serverName + ']';
        variablesInput.value = JSON.stringify(selectedVariables);
        form.appendChild(variablesInput);
    });

    // Log the form data to the console
    var formData = new FormData(form);
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }


}








    function populateServers(existingServers) {
    var container = document.getElementById('servers-container');

    existingServers.forEach(function (server) {
        var newServerInput = document.createElement('div');
        newServerInput.className = 'server-input';

        // Initialize variables for the existing server
        var serverNameInput = '<label>Server Name: <input type="text" name="server_names[]" value="' + server.name + '"></label>';
        var serverUrlInput = '<label>URL: <input type="text" name="server_urls[]" value="' + server.url + '"></label>';
        
        var variablesContainer = '<div class="variables-container">';
        
        server.variables.forEach(function (variable) {
            var isChecked = true; // Initialize as true if the variable exists in server.variables
            variablesContainer += '<label><input type="checkbox" name="variables[' + server.name + '][]" value="' + variable + '" ' + (isChecked ? 'checked' : '') + '> ' + variable + '</label>';
        });

        variablesContainer += '</div>';

        // Add server inputs and variables to the new server div
        newServerInput.innerHTML = serverNameInput + serverUrlInput + variablesContainer +
            '<span class="remove-server" onclick="removeServer(this)">X</span>';

        container.appendChild(newServerInput);
    });
}

function fetchVariables() {
    fetch('extract_variables.php')
        .then(response => response.json())
        .then(variables => displayVariables(variables))
        .catch(error => console.error('Error fetching variables:', error));

    // Append the loaded code to the URL when fetching server data
    const loadedCode = new URLSearchParams(window.location.search).get("loadedCode");
    const serversUrl = 'get_servers.php' + (loadedCode ? `?loadedCode=${loadedCode}` : '');

    fetch(serversUrl)
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server data not found. Status: ${response.status} ${response.statusText}`);
        }
        return response.text();  // Change response.json() to response.text()
    })
    .then(data => {
        // Extract JSON part of the response
        const jsonStartIndex = data.indexOf('[');
        const jsonData = jsonStartIndex !== -1 ? data.slice(jsonStartIndex) : '[]';
        return JSON.parse(jsonData);
    })
    .then(existingServers => populateServers(existingServers))
    .catch(error => console.error('Error fetching existing servers:', error));

}


    // Function to display variables in the container
    function displayVariables(variables) {
    const variablesContainer = document.getElementById('variables-container');
    const variablesList = document.createElement('ul');

    variables.forEach(variableItem => {
        const variableListItem = document.createElement('li');
        const variableNames = variableItem.variables.join(', ');
        variableListItem.textContent = `[${variableNames}] '${variableItem.long_name}', '${variableItem.short_name}'`;
        variablesList.appendChild(variableListItem);
    });

    variablesContainer.appendChild(variablesList);
}

    // Call the fetch function on page load
    window.onload = function () {
        fetchVariables();
    };



</script>




</body>
</html>
