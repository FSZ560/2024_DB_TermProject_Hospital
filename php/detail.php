<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>詳細資料</title>
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
    <h1>詳細資料</h1>
    <table>
        <tbody>
<?php
// 資料庫連線參數
$servername = "localhost"; 
$username = "root";
$password = "DB_team_11_password"; 
$dbname = "db_team_11_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 取得 Patient ID
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : '';

// 查詢 SQL 語句
$sql = "SELECT 
            appointment.appointment_id, 
            appointment.patient_id, 
            patient.height, 
            patient.weight, 
            person.last_name, 
            person.first_name, 
            person.phone, 
            person.id_card
        FROM 
            appointment
        INNER JOIN 
            patient ON appointment.patient_id = patient.person_id
        INNER JOIN 
            person ON patient.person_id = person.person_id
        WHERE 
            appointment.patient_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// 檢查是否有結果
if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Last Name</th><th>First Name</th><th>Phone</th><th>ID Card</th><th>Height</th><th>Weight</th></tr>";
    echo "<tr>";
	$row = $result->fetch_assoc();
    echo "<td>" . $row["last_name"] . "</td>";
    echo "<td>" . $row["first_name"] . "</td>";
    echo "<td>" . $row["phone"] . "</td>";
    echo "<td>" . $row["id_card"] . "</td>";
    echo "<td>" . $row["height"] . "</td>";
    echo "<td>" . $row["weight"] . "</td>";
    echo "</tr>";
    echo "</table>";
} else {
    echo "沒有找到對應的病人資料。";
}

$stmt->close();
$conn->close();
?>
        </tbody>
    </table>
    <div class="pagination">
    </div>
</body>
</html>
