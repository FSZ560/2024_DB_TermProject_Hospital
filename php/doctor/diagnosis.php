<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>診斷書</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #f4f4f4;
            cursor: pointer;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #007bff;
        }
        .pagination a:hover {
            background-color: #007bff;
            color: white;
        }
        .pagination span.current {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .action-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .action-icons a {
            color: inherit;
            text-decoration: none;
            font-size: 16px;
        }
        .action-icons a.edit {
            color: blue;
        }
        .action-icons a.delete {
            color: red;
        }
    </style>
</head>
<body>
    <h1>診斷書</h1>
    <table>
        <tbody>
<?php
// 資料庫連接設定
$servername = "localhost"; 
$username = "root"; 
$password = "DB_team_11_password";
$dbname = "db_team_11_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

$record_id = isset($_GET['record_id']) ? $_GET['record_id'] : '';

// 從資料表中選取資料
$sql = "SELECT * FROM medical_certificate WHERE record_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr>
			<th>certificate_id</th>
			<th>record_id</th>
			<th>prescription</th>
		</tr>";
	echo "<tr>";
    $row = $result->fetch_assoc(); 
    echo "<td>" . $row["certificate_id"] . "</td>";
	echo "<td>" . $row["record_id"] . "</td>";
	echo "<td>" . $row["prescription"] . "</td>";
	echo "</tr>";

    echo "</table>";
} else {
    echo "資料表為空";
}

// 關閉連接
$conn->close();
?>
        </tbody>
    </table>
    <div class="pagination">
    </div>
</body>
</html>
