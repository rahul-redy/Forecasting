<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">

    <style>
        /* Customize text input box */
        

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        h1, h2 {
            font-family: 'Anton', sans-serif; /* Use Anton font for titles */
        }
        h1 {
        text-align: center; /* Center-align the configuration title */
    }
        body {
            background-image: url('sky2.jpeg'); /* Set background image */
            /*background-color: #f1f3f1; */
            background-size: cover; /* Cover the entire background */
            background-position: center; /* Center the background image */
            background-repeat: repeat-y; /* Do not repeat the background image */
            font-family: 'Arial', sans-serif; /* Specify font-family */
            color: #333; /* Set text color */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
        }

        textarea, input[type="text"]{
            width: calc(100% - 24px);
            padding: 10px;
            border: 2px solid #333; /* Adjust border thickness */
            border-radius: 10px; /* Round corners */
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.7); /* Set background color with 30% opacity */
            font-family: 'Reddit Mono', sans-serif;
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
            /*background-color: #FFD088;*/
        }

        .remove-server {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
            
            color: black; /* Change color for better visibility */
        }

        
        .remove-server img {
    width: 16px; /* Adjust width as needed */
    height: 16px; /* Adjust height as needed */
    cursor: pointer; /* Add pointer cursor to indicate clickable element */
}

        .remove-server:hover {
            color: red; /* Change color on hover */
        }

        label {
        display: block;
        margin-bottom: 10px; /* Add margin bottom to create a gap between label elements */
    }


        .variables-container {
            position: relative;
            border: 2px solid black;
            border-radius: 10px;
            padding: 10px;
            margin-top: 10px;
            max-height: 200px; /* Set a maximum height for the container */
            overflow-y: auto; /* Enable vertical scrolling if content exceeds the maximum height */
        }

        .variables-container label {
            display: block;
            margin-bottom: 5px;
        }

        /* Set text color to black for server name, server URL, and variables checkboxes */
        .server-input label,
        .variables-container label {
            color: black;
        }

        .hover-container {
    position: absolute;
    top: 0px; /* Adjust top position */
    right: 0px; /* Adjust right position */
    background-color: #333; /* Set transparency to 50% */
    border: 1px solid #ccc;
    padding: 10px;
    z-index: 1;
    display: block; /* Display by default */
}

.server-input:hover .hover-container {
    display: block; /* Display on hover */
}

        .edit-variable-btn {
    background-color: #fff; /* White background color */
    color: #333; /* Text color */
    border: 1px solid #333; /* Add border */
    padding: 2px 4px; /* Adjust padding */
    font-size: 10px; /* Adjust font size */
    border-radius: 5px; /* Add border-radius for rounded corners */
    cursor: pointer;
}


    .save-button {
        background-color: green;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
    }






        /* Add hover container styles as needed */

    </style>
</head>
<body>

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

            $selectedVariables = $server['variables'] ?? array();

            $allVariables = array(
                'ubar_eastward, vbar_northward',
                'u_sur_eastward, v_sur_northward',
                'zeta', 'Hwave',
                'temp_sur', 'salt_sur'
            );


            foreach ($allVariables as $variable) {
                $isChecked = in_array($variable, $selectedVariables);
                echo '<div>';
                echo '<label>';
                echo '<input type="checkbox" name="variables[' . htmlspecialchars($server['name']) . '][]" value="' . $variable . '" ' . ($isChecked ? 'checked' : '') . '> ' . $variable;
                echo '<button type="button" class="edit-variable-btn" onclick="toggleOptionsContainer(this)">Edit</button>';
                echo '</label>';
                echo '</div>';
            }
            echo '</div>';
            
            echo '<span class="remove-server" onclick="removeServer(this)"><img src="remove.png" alt="Remove"></span>';

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
            '<span class="remove-server" onclick="removeServer(this)"><img src="remove.png" alt="Remove"></span>';

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
        console.log('Removing server...');
        var container = document.getElementById('servers-container');
        console.log('Container:', container);
        var serverInput = element.closest('.server-input');
        console.log('Server Input:', serverInput);

        container.removeChild(serverInput);
        console.log('Server removed successfully.');

        // Get the server name
        var serverName = serverInput.querySelector('input[name^="server_names"]').value;
        console.log('Server Name:', serverName);
        // Add the server name to the array of deleted servers
        deletedServers.push(serverName);
        console.log('Deleted Servers:', deletedServers);

    }

    // Modify the form submission logic
    function inspectAndSubmit(event) {
    // Prevent the default form submission behavior
    event.preventDefault();

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
    serverInputs.forEach(function (serverInput, index) {
        var serverName = serverInput.querySelector('input[name^="server_names"]').value;
        var selectedVariables = [];
        var checkboxes = serverInput.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedVariables.push(checkbox.value);

                // Check if properties already exist for this variable and server
                var existingPropertiesInput = serverInput.querySelector('input[name^="variable_properties[' + serverName + '][' + checkbox.value + ']"]');
                if (!existingPropertiesInput) {
                    // Create a hidden input field for default properties
                    var defaultProperties = getDefaultProperties(checkbox.value); // Assuming a function getDefaultProperties exists
                    var hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'variable_properties[' + serverName + '][' + checkbox.value + ']';
                    hiddenInput.value = JSON.stringify(defaultProperties);
                    form.appendChild(hiddenInput);
                }
            }
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

    // Submit the form
    form.submit();
}

function getDefaultProperties(variableName) {
    // Define default properties for each variable here
    var defaultProperties = {
        "layertype": "dynmap",
        "varthreshold": "1000.0",
        "varscale": "1.0",
        "longname": "Enter a longname here",
        "shortname": "Enter a shortname here",
        "units": "enter units here",
        "colorbar": "zeta"
    };

    // Override default properties based on variableName
    switch (variableName) {
        case 'u_sur_eastward, v_sur_northward':
            defaultProperties.colorbar = "velocity";
            defaultProperties.longname = "Srfc. curr.";
            defaultProperties.shortname = "Surface currents";
            defaultProperties.units = "m/s";
            break;
        case 'zeta':
            defaultProperties.longname = "Water level";
            defaultProperties.shortname = "Water lvl.";
            defaultProperties.units = "m";
            break;
        case 'Hwave':
            defaultProperties.colorbar = "Hm0";
            defaultProperties.longname = "Wave amplitude";
            defaultProperties.shortname = "Hm0";
            defaultProperties.units = "m";
            break;
        case 'Risk':
            defaultProperties.colorbar = "zeta";
            defaultProperties.longname = "Damage level";
            defaultProperties.shortname = "Damage";
            defaultProperties.units = "m/s";
            break;
        case 'temp_sur':
            defaultProperties.colorbar = "temperature";
            defaultProperties.longname = "Surface temperature";
            defaultProperties.shortname = "SST";
            defaultProperties.units = "°C";
            break;
        case 'salt_sur':
            defaultProperties.colorbar = "salinity";
            defaultProperties.longname = "Surface salinity";
            defaultProperties.shortname = "SSS";
            defaultProperties.units = "";
            break;
        // Add more cases as needed
    }


    return defaultProperties;
}

    // Call the fetch function on page load
    window.onload = function () {
        fetchVariables();
    };

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

    async function fetchVariables() {
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
    var isChecked = false; // Initialize as true if the variable exists in server.variables
    var defaultProperties = getDefaultProperties(variable); // Get default properties for the variable
    variablesContainer += '<div>';
    variablesContainer += '<label>';
    variablesContainer += '<input type="checkbox" name="variables[' + server.name + '][]" value="' + variable + '" ' + (isChecked ? 'checked' : '') + '> ' + variable;
    variablesContainer += '<button type="button" class="edit-variable-btn">Edit Properties</button>'; // Removed inline onclick attribute
    variablesContainer += '</label>';
    variablesContainer += '</div>';
});


        variablesContainer += '</div>';
        

        // Add server inputs and variables to the new server div
        newServerInput.innerHTML = serverNameInput + serverUrlInput + variablesContainer +
                '<span class="remove-server" onclick="removeServer(this)"><img src="remove.png" alt="Remove"></span>'; // Moved it here
                // Keep hover container empty for future use if needed

        container.appendChild(newServerInput);
    });

    // Attach event listeners to "Edit Properties" buttons for both existing and newly added servers
    var editButtons = document.querySelectorAll('.edit-variable-btn');
    editButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            toggleOptionsContainer(button);
        });
    });
}




    function addOptionsContainer(button) {
    // Create a new container for options
    var optionsContainer = document.createElement('div');
    optionsContainer.className = 'options-container';

    // Add input fields for each property
    var optionsList = document.createElement('ul');

    // Layertype
    var layertypeInput = document.createElement('li');
    layertypeInput.innerHTML = '<label>Layertype: <select name="layertype"><option value="dynmap">dynmap</option><option value="dynscatter">dynscatter</option></select></label>';
    optionsList.appendChild(layertypeInput);

    // Varthreshold
    var varthresholdInput = document.createElement('li');
    varthresholdInput.innerHTML = '<label>Varthreshold: <input type="number" name="varthreshold" value="1000.0"></label>';
    optionsList.appendChild(varthresholdInput);

    // Varscale
    var varscaleInput = document.createElement('li');
    varscaleInput.innerHTML = '<label>Varscale: <input type="number" name="varscale" value="1.0"></label>';
    optionsList.appendChild(varscaleInput);

    // Longname
    var longnameInput = document.createElement('li');
    longnameInput.innerHTML = '<label>Longname: <input type="text" name="longname" value="Enter a longname here"></label>';
    optionsList.appendChild(longnameInput);

    // Shortname
    var shortnameInput = document.createElement('li');
    shortnameInput.innerHTML = '<label>Shortname: <input type="text" name="shortname" value="Enter a shortname here"></label>';
    optionsList.appendChild(shortnameInput);

    // Units
    var unitsInput = document.createElement('li');
    unitsInput.innerHTML = '<label>Units: <input type="text" name="units" value="enter units here"></label>';
    optionsList.appendChild(unitsInput);

    // Colorbar
    var colorbarInput = document.createElement('li');
    colorbarInput.innerHTML = '<label>Colorbar: <select name="colorbar"><option value="bathy">bathy</option><option value="zeta" selected>zeta</option><option value="Hm0">Hm0</option><option value="temperature">temperature</option><option value="salinity">salinity</option><option value="Qair">Qair</option><option value="velocity">velocity</option><option value="salt">salt</option></select></label>';
    optionsList.appendChild(colorbarInput);

    optionsContainer.appendChild(optionsList);

    // Add save button
    var saveButton = document.createElement('button');
    saveButton.textContent = 'Save';
    saveButton.className = 'save-button'; // Add a class for styling
    saveButton.onclick = function () {
        saveProperties(optionsList, button);
    };
    optionsContainer.appendChild(saveButton);

    // Insert the options container below the button
    button.parentNode.insertBefore(optionsContainer, button.nextSibling);
}



function saveProperties(optionsList, button) {
    // Get the variable name associated with the button
    var variableName = button.parentNode.querySelector('input[type="checkbox"]').value;
    var serverName = button.closest('.server-input').querySelector('input[name^="server_names"]').value;

    // Check if properties already exist for this variable and server
    var existingPropertiesInput = button.parentNode.querySelector('input[name^="variable_properties[' + serverName + '][' + variableName + ']"]');
    var properties = existingPropertiesInput ? JSON.parse(existingPropertiesInput.value) : {};

    // Iterate through input fields and update the properties
    optionsList.querySelectorAll('input, select').forEach(function(input) {
        properties[input.name] = input.value;
    });

    // Close options container
    var optionsContainer = button.parentNode.querySelector('.options-container');
    if (optionsContainer) {
        optionsContainer.remove();
    }

    // Update or create the hidden input field with the serialized properties
    var hiddenInput;
    if (existingPropertiesInput) {
        existingPropertiesInput.value = JSON.stringify(properties);
        hiddenInput = existingPropertiesInput;
    } else {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'variable_properties[' + serverName + '][' + variableName + ']';
        hiddenInput.value = JSON.stringify(properties);
        button.parentNode.appendChild(hiddenInput);
    }

    // Log the saved properties to the console
    console.log('Saved properties for variable', variableName + ' in server ' + serverName + ':', properties);
}


function toggleOptionsContainer(button) {
    // Check if options container already exists
    var optionsContainer = button.parentNode.querySelector('.options-container');
    if (optionsContainer) {
        // If exists, toggle its display
        if (optionsContainer.style.display === 'block') {
            optionsContainer.style.display = 'none';
        } else {
            optionsContainer.style.display = 'block';
        }
    } else {
        // If doesn't exist, add options container
        addOptionsContainer(button);
    }
}


</script>

</body>
</html>
