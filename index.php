<?php

// Start the session
session_start();

// Database credentials array (write your credentials in here)
$dbCredentials = [
    'servername' => 'localhost',
    'username' => '',
    'password' => '',
    'dbname' => ''
];

/**
 * Connect to the database using the provided credentials
 *
 * @param array $dbCredentials The database credentials (servername, username, password, dbname)
 * @return mysqli|false Returns the database connection object, or false if the connection failed
 */
function dbConnect($dbCredentials) {
    
    // Establish a database connection with those credentials
    $mysqli = new mysqli(
        $dbCredentials['servername'],
        $dbCredentials['username'],
        $dbCredentials['password'],
        $dbCredentials['dbname']
    );

    // Check database connection
    if ($mysqli -> connect_errno) {
        
        // Die if connection fails and display a message
        die("Failed to connect to MySQL: " . $mysqli->connect_error);

    }

    // Return the database connection object on successful connection
    return $mysqli;
}

// Call the function to establish the database connection
$mysqli = dbConnect($dbCredentials);

/**
 * Check if the session is active and the user ID is set
 *
 * @return bool Returns true if the user session is active and the user ID is set, false otherwise
 */
function isUserSessionSet() {
    return isset($_SESSION['user_id']);
}

### Functions for database queries

/**
 * Retrieve the sectors data from the database
 *
 * @param mysqli $mysqli The database connection object
 * @return array|null Return an array containing the sectors data if found, otherwise null
 */
function getSectors($mysqli) {

    // Retrieve the sectors data from the database with a prepared statement
    $selectSectorsQuery = "SELECT * FROM sectors";
    $selectSectorsStmt = mysqli_prepare($mysqli, $selectSectorsQuery);
    mysqli_stmt_execute($selectSectorsStmt);
    $selectSectorsResult = mysqli_stmt_get_result($selectSectorsStmt);

    // Create an empty array to store the sectors data
    $sectors = [];

    // Check if the query was successful and whether it returned any data
    if ($selectSectorsResult && mysqli_num_rows($selectSectorsResult) > 0) {

        // Fetch each row from the result set and add it to the $sectors array
        while ($row = mysqli_fetch_assoc($selectSectorsResult)) {
            $sectors[] = $row;
        }

    } else {

        // If the result set is empty, set $sectors to null 
        $sectors = null;
    }

    // Return the $sectors array of the retrieved sector data
    return $sectors;
}

/**
 * Retrieve user data from the database based on the provided user ID
 *
 * @param mysqli $mysqli The database connection object
 * @param string $userId The ID of the user to retrieve data for
 * @return array|null Returns an array containing the user data if found, otherwise null
 */
function getUserData($mysqli, $userId) {

    // Retrieve the user data from the database with a prepared statement
    $selectUserQuery = "SELECT * FROM users WHERE user_id = ?";
    $selectUserStmt = mysqli_prepare($mysqli, $selectUserQuery);
    mysqli_stmt_bind_param($selectUserStmt, 's', $userId);
    mysqli_stmt_execute($selectUserStmt);
    $selectUserResult = mysqli_stmt_get_result($selectUserStmt);

    // Check if the query was successful and whether it returned any data
    if ($selectUserResult && mysqli_num_rows($selectUserResult) > 0) {

        // Retrieve the user data from the database result set
        $userData = mysqli_fetch_assoc($selectUserResult);
        
    } else {

        // If the result set is empty, set $userData to null 
        $userData = null;

    }

    // Return the $userData array of the retrieved user data
    return $userData;
}

/**
 * Update user data in the database based on the provided user ID
 *
 * @param mysqli $mysqli The database connection object
 * @param string $userName The new name of the user
 * @param int $sectorValue The new value of the sector
 * @param string $terms The new user's agreement to terms
 * @param string $sectorName The new name of the sector (used for readability in the users table only)
 * @param string $userId The ID of the user to update data for
 * @return bool Returns true if the update was successful, false otherwise
 */
function updateUserData($mysqli, $userName, $sectorValue, $terms, $sectorName, $userId) {

    // Update existing record in database with a prepared statement
    $updateUserQuery = "UPDATE users SET user_name = ?, sector_value = ?, terms = ?, sector_name = ? WHERE user_id = ?";
    $updateUserStmt = mysqli_prepare($mysqli, $updateUserQuery);
    mysqli_stmt_bind_param($updateUserStmt, 'sisss', $userName, $sectorValue, $terms, $sectorName, $userId);
    mysqli_stmt_execute($updateUserStmt);
    
    // Return true if the database update was successful and affected any rows, otherwise false
    return mysqli_stmt_affected_rows($updateUserStmt) > 0;
}

/**
 * Insert a new user into the database
 *
 * @param mysqli $mysqli The database connection object
 * @param string $userName The name of the user
 * @param int $sectorValue The sector value associated with the user
 * @param string $terms The user's agreement to terms
 * @param string $sectorName The name of the sector (used for readability in the users table only)
 * @return bool Returns true if the insertion was successful, false otherwise
 */
function insertUserData($mysqli, $userName, $sectorValue, $terms, $sectorName) {

    // Generate a unique user identifier
    $userId = uniqid();

    // Insert the new record into the database with a prepared statement
    $insertUserQuery = "INSERT INTO users (user_id, user_name, sector_value, terms, sector_name) VALUES (?, ?, ?, ?, ?)";
    $insertUserStmt = mysqli_prepare($mysqli, $insertUserQuery);
    mysqli_stmt_bind_param($insertUserStmt, 'ssiss', $userId, $userName, $sectorValue, $terms, $sectorName);
    mysqli_stmt_execute($insertUserStmt);

    // Check if the insertion was successful
    if (mysqli_stmt_affected_rows($insertUserStmt) > 0) {

        // Set the user identifier in the session
        $_SESSION['user_id'] = $userId;
        return true;

    } else {

        return false;

    }
}

// Initialize variables with empty values
$userName = '';
$sectorValue = 0;
$terms = '';
$message = '';

// Retrieve the sectors data from the database using the function
$sectors = getSectors($mysqli);

// Check if $sectors is not empty (truthy) and assign value to $message depending on that
$message = $sectors ? '' : "No sectors found.";

### Functions for displaying sectors

/**
 * Assigns levels to sectors based on their parent-child relationship
 *
 * @param array $sectors The array of sectors to assign levels to
 * @param int|null $parentId The parent ID to start the assignment from (default is null for top-level parents)
 * @param int $level The level to start with (default is 1 for top-level parents)
 * @return void Assigns the 'level' key to each sector in the input array
 */
function assignLevels(&$sectors, $parentId = 0, $level = 1) {

    foreach ($sectors as &$sector) {

        // Check if current sector matches the specified parent's child
        if ($sector['parent_id'] == $parentId) {

            // Assign the level to the current sector
            $sector['level'] = $level;

            // Recursively assign levels to child sectors
            assignLevels($sectors, $sector['id'], $level + 1);
        }
    }
}

/**
 * Generate the HTML options for each parent and its children based on the sector groups
 *
 * @param array $sectorGroups An associative array containing sectors grouped by their parent_id
 * @param int $parentId The ID of the parent sector
 * @param string $indentation The indentation string for child sectors to represent the hierarchy
 * @param int $sectorValue The selected sector value to mark as 'selected'
 * @return void Outputs the generated HTML options directly
 */
function generateOptions($sectorGroups, $parentId, $indentation = '', $sectorValue = 0) {
    
    // Check if the parent sector has children (sub-sectors)
    if (isset($sectorGroups[$parentId])) {

        foreach ($sectorGroups[$parentId] as $sector) {

            $optionValue = $sector['sector_value'];
            $optionLabel = $indentation . htmlspecialchars($sector['sector_name']);

            // Check if the current option value matches the selected sector value
            $selectedAttribute = $optionValue === $sectorValue ? 'selected' : '';

            // Generate HTML option tag using $optionValue and $optionLabel
            echo '<option value="' . $optionValue . '" ' . $selectedAttribute . '>' . $optionLabel . '</option>';

            // Generate options for children recursively
            generateOptions($sectorGroups, $sector['id'], $indentation . '&nbsp;&nbsp;&nbsp;', $sectorValue);
        }
    }
}

/**
 * Generate HTML optgroups and options within them based on the sectors data and sector groups
 *
 * @param array $sectors An array containing all the sectors data
 * @param array $sectorGroups An associative array grouping sectors by their parent_id
 * @param int $sectorValue The selected sector value for which the options are generated
 * @return void Outputs the generated HTML optgroups and options directly
 */
function generateOptgroups($sectors, $sectorGroups, $sectorValue) {

    // Generate html optgroups for the top-level parents (null parent_id)
    foreach ($sectors as $sector) {

        // Check if the sector is a top-level parent
        if ($sector['parent_id'] === null) {

            // Start an optgroup with the label as the sector name
            echo '<optgroup label="' . htmlspecialchars($sector['sector_name']) . '">';

            // Generate options for the current parent and its children with function
            generateOptions($sectorGroups, $sector['id'], '', $sectorValue);

            echo '</optgroup>';
        }
    }
}

// Check if the session is active and the user ID is set with the function
if (isUserSessionSet()) {

    // Get the user ID from the session and retrieve the user's data from the database using the function
    $userData = getUserData($mysqli, $_SESSION['user_id']);

    // Check if $userData is not empty (truthy) and assign values to variables to populate the form fields
    if ($userData) {

        $userName = $userData['user_name'];
        $sectorValue = $userData['sector_value'];
        $terms = $userData['terms'];
        $message = '';

    } else {

        // If no data is found for the current user, set the message accordingly
        $message = "No data found for the current user.";

    }

} else {

    $message = '';

}

// Check if the form is submitted via post method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate, sanitize and assign the fields
    $userName = isset($_POST["name"]) ? trim($_POST["name"]) : '';
    $sectorValue = isset($_POST["sectors"]) ? (int)$_POST["sectors"] : 0;
    $terms = isset($_POST["terms"]) && $_POST["terms"] === "on" ? "agreed" : "denied";

    // Validate the name field
    if (empty($userName)) {
        $message = "Please enter your name.";
    } elseif (!preg_match('/^[A-Za-z\-\'\säõüöÄÕÜÖ]{1,50}$/u', $userName)) {
        $message = "Please enter a valid name.";
    }

    // Validate the sector selection
    elseif (empty($sectorValue)) {
        $message = "Please select a sector.";
    }

    // Validate the terms agreement
    elseif ($terms !== "agreed") {
        $message = "You must agree to the terms.";
    }

    // All fields are valid, proceed with data insertion or update
    else {

        // Get the sector name based on the selected sector value
        $sectorName = '';

        foreach ($sectors as $sector) {

            if ($sector['sector_value'] == $sectorValue) {

                $sectorName = $sector['sector_name'];
                break;

            }
        }

        // Check if the session is active and the user ID is set with the function
        if (isUserSessionSet()) {

            // If the user ID is set, update the existing record in the database
            $userId = $_SESSION['user_id'];

            // Compare the new submitted data with the user data from the database to make sure that there is anything to update
            if ($userName === $userData['user_name'] && $sectorValue === (int) $userData['sector_value'] && $terms === $userData['terms']) {

                $message = "Nothing to update.";

            } else {

                // If there is anything to update, use the function to update user data
                $updateResult = updateUserData($mysqli, $userName, $sectorValue, $terms, $sectorName, $userId);

                // Set the message based on the update result
                $message = $updateResult ? "Data has been successfully updated." : "Unable to update the data.";

            }

        } else {

            // If the user ID is not set, use the function to insert a new user into the database
            $insertResult = insertUserData($mysqli, $userName, $sectorValue, $terms, $sectorName);

            // Set the message based on the insert result
            $message = $insertResult ? "Data has been successfully saved." : "Unable to save the data.";

        }
    }
}

// Call the function to assign levels to sectors
assignLevels($sectors);

// Group the sectors by their parent_id
$sectorGroups = [];

foreach ($sectors as $sector) {

    $parent = $sector['parent_id'];
    $sectorGroups[$parent][] = $sector;

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User form</title>
</head>
<body>

<section>
    <h3>Please enter your name and pick the Sectors you are currently involved in.</h3>
    <!-- Form for user data -->
    <form method="POST">
        <label for="name">Name: </label>
        <!-- Input field to enter user's name with PHP variable to display the user's name as the default value -->
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($userName); ?>">
        <br><br>
        <label for="sectors">Sector: </label>
        <!-- Dropdown list for selecting sectors -->
        <select name="sectors" id="sectors">
        <!-- Option to show default sector as "Select your sector" before choosing any sectors -->
        <option value="0" disabled selected hidden>Select your sector</option>
        <?php
            // Call the function to generate optgroups and options for sectors
            generateOptgroups($sectors, $sectorGroups, $sectorValue);
        ?>
        </select>
        <br><br>
        <label for="terms">Agree to terms:</label>
        <!-- Checkbox to agree to terms with PHP variable to check and change checkbox status -->
        <input type="checkbox" id="terms" name="terms" <?= $terms === 'agreed' ? 'checked' : ''; ?>>
        <br><br>
        <!-- Submit button to save the form -->
        <input type="submit" value="Save" name="form">
    </form>
    <!-- Display messages to show the result of form submission or any errors -->
    <p><?= $message ?></p>
</section>

</body>
</html>