<?php
// Database connection parameters
$host = "localhost";
$port = "5432"; // Default port for PostgreSQL
$username = "postgres"; // Change this to your PostgreSQL username
$password = "postgres"; // Change this to your PostgreSQL password
$database = "floatr";

// Create connection
$conn = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Get the selected table name from the AJAX request
$tableName = $_POST['tableName'];

// Function to retrieve table data based on table name
function getTableData($tableName, $conn)
{
    $html = '';

    // Get table structure
    $sqlStructure = "SELECT column_name FROM information_schema.columns WHERE table_name = '$tableName'";
    $structureResult = pg_query($conn, $sqlStructure);
    if ($structureResult) {
        $html .= "<h2>$tableName</h2>";
        $html .= "<table id='$tableName' class='display'>";
        $html .= "<thead><tr>";
        while ($row = pg_fetch_assoc($structureResult)) {
            $html .= "<th>{$row['column_name']}</th>";
        }
        $html .= "</tr></thead>";
    }

    // Get table data
    $sqlData = "SELECT * FROM $tableName";
    $dataResult = pg_query($conn, $sqlData);
    if ($dataResult) {
        $html .= "<tbody>";
        while ($row = pg_fetch_assoc($dataResult)) {
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
pg_close($conn);

// Return the HTML table data
echo $tableDataHTML;
