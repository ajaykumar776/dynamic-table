<?php
// Database connection parameters
$host = "localhost";
$port = "5432"; // Default port for PostgreSQL
$username = "postgres"; // Change this to your PostgreSQL username
$password = ""; // Change this to your PostgreSQL password
$database = "floatr";

// Create connection
$conn = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Get all tables in the database
$tables = [];
$sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";
$result = pg_query($conn, $sql);

if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $tables[] = $row['table_name'];
    }
}

// Function to retrieve table structure and data
function getTableStructureAndData($tableName, $conn)
{
    $result = [];

    // Get table structure
    $structure = [];
    $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '$tableName'";
    $structureResult = pg_query($conn, $sql);
    if ($structureResult) {
        while ($row = pg_fetch_assoc($structureResult)) {
            $structure[] = $row['column_name'];
        }
    }
    $result['structure'] = $structure;

    // Get table data
    $data = [];
    $sql = "SELECT * FROM $tableName";
    $dataResult = pg_query($conn, $sql);
    if ($dataResult) {
        while ($row = pg_fetch_assoc($dataResult)) {
            $data[] = $row;
        }
    }
    $result['data'] = $data;

    return $result;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Tables</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: none;
            overflow-x: auto;
            /* Enable horizontal scrolling */
            white-space: nowrap;
            /* Prevent text wrapping */
        }

        table.dataTable th,
        table.dataTable td {
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #aaa;
            border-radius: 3px;
            padding: 16px;
            background-color: transparent;
            margin-left: 40px;
        }

        .dataTables_length {
            margin-bottom: 30px !important;
        }
    </style>
</head>

<body>
    <h1>Database Tables</h1>
    <label for="tableSelect">Select Table:</label>
    <select id="tableSelect">
        <option value="">Select</option>
        <?php foreach ($tables as $table) : ?>
            <option value="<?php echo $table; ?>"><?php echo $table; ?></option>
        <?php endforeach; ?>
    </select>

    <div id="tableContainer"></div>

    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Include DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#tableSelect').change(function() {
                var selectedTable = $(this).val();
                $.ajax({
                    url: 'postgres.php', // PHP script to fetch table data
                    type: 'POST',
                    data: {
                        tableName: selectedTable
                    },
                    success: function(response) {
                        $('#tableContainer').html(response); // Populate table data
                        $('table.display').DataTable({
                            "paging": true, // Enable pagination
                            "pageLength": 10 // Set number of rows per page
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>