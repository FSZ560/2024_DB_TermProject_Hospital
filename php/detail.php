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
$servername = "localhost"; // 根據你的伺服器設定調整
$username = "root"; // 根據你的伺服器設定調整
$password = "DB_team_11_password"; 
$dbname = "db_team_11_project";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 查詢 SQL 語句
$sql = "SELECT * FROM clinic";
$result = $conn->query($sql);

// 檢查是否有結果
if ($result->num_rows > 0) {
    // 顯示資料
    echo "<table border='1'>";
    echo "<tr><th>clinic_id</th><th>clinic_date</th><th>period</th><th>department_id</th><th>location</th><th>doctor_id</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["clinic_id"] . "</td>";
        echo "<td>" . $row["clinic_date"] . "</td>";
        echo "<td>" . $row["period"] . "</td>";
        echo "<td>" . $row["department_id"] . "</td>";
        echo "<td>" . $row["location"] . "</td>";
        echo "<td>" . $row["doctor_id"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 結果";
}

// 關閉連線
$conn->close();
?>
        </tbody>
    </table>
    <div class="pagination">
    </div>
</body>
</html>