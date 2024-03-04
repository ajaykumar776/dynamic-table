<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "school";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the selected table name from the AJAX request
$tableName = $_POST['tableName'];

// Function to retrieve table data based on table name
function getTableData($tableName, $conn)
{
    $html = '';

    // Get table structure
    $sqlStructure = "DESCRIBE $tableName";
    $structureResult = mysqli_query($conn, $sqlStructure);
    if ($structureResult) {
        $html .= "<h2>$tableName</h2>";
        $html .= "<table id='$tableName' class='display'>";
        $html .= "<thead><tr>";
        while ($row = mysqli_fetch_assoc($structureResult)) {
            $html .= "<th>{$row['Field']}</th>";
        }
        $html .= "</tr></thead>";
    }

    // Get table data
    $sqlData = "SELECT * FROM $tableName";
    $dataResult = mysqli_query($conn, $sqlData);
    if ($dataResult) {
        $html .= "<tbody>";
        while ($row = mysqli_fetch_assoc($dataResult)) {
            $html .= "<tr>";
            foreach ($row as $value) {
                $html .= "<td>$value</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
    }

    return $html;
}

// Retrieve table data for the selected table
$tableDataHTML = getTableData($tableName, $conn);

// Close the database connection
mysqli_close($conn);

// Return the HTML table data
echo $tableDataHTML;
