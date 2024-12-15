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
$servername = "localhost"; // 根據伺服器調整
$username = "root"; // 資料庫使用者名稱
$password = "DB_team_11_password"; // 資料庫密碼
$dbname = "db_team_11_project"; // 資料庫名稱

// 建立連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 分頁參數
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// 從資料表中選取資料
$sql = "SELECT * FROM medical_certificate";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr>
			<th>certificate_id</th>
			<th>record_id</th>
			<th>prescription</th>
		</tr>";

    // 輸出每一行資料
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["certificate_id"] . "</td><td>" . $row["record_id"] . "</td><td>" . $row["prescription"] . "</td></tr>";
    }

    echo "</table>";
} else {
    echo "資料表為空";
}

// 分頁邏輯
$sql_total = "SELECT COUNT(*) AS total FROM appointment";
$total_records = $conn->query($sql_total)->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

echo "</tbody></table><div class='pagination'>";
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        echo "<span class='current'>$i</span>";
    } else {
        echo "<a href='?page=$i'>$i</a>";
    }
}
echo "</div>";

// 關閉連接
$conn->close();
?>
        </tbody>
    </table>
    <div class="pagination">
    </div>
</body>
</html>