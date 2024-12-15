<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掛號記錄查詢</title>
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
    <h1>掛號記錄查詢</h1>
    <table>
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Sequence No</th>
                <th>Clinic</th>
                <th>Patient ID</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<?php
// 資料庫連線資訊
$host = "localhost";
$username = "root";
$password = "DB_team_11_password";
$database = "db_team_11_project";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}

// 處理刪除操作
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM appointment WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('記錄已刪除'); window.location.href='?page=1';</script>";
    } else {
        echo "<script>alert('刪除失敗');</script>";
    }
    $stmt->close();
}

// 獲取篩選條件
$year_filter = isset($_GET['year']) ? $_GET['year'] : '';
$order_filter = isset($_GET['order']) ? $_GET['order'] : 'DESC'; // 預設由新到舊
$clinic_filter = isset($_GET['clinic']) ? $_GET['clinic'] : '';

// 建立查詢條件
$where_conditions = [];
if ($year_filter) {
    $where_conditions[] = "YEAR(register_time) = '$year_filter'";
}
if ($clinic_filter) {
    $where_conditions[] = "clinic_id = '$clinic_filter'";
}
$where_sql = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// 獲取可用的年份和科別
$year_result = $conn->query("SELECT DISTINCT YEAR(register_time) AS year FROM appointment ORDER BY year DESC");
$clinic_result = $conn->query("SELECT DISTINCT clinic_id FROM appointment ORDER BY clinic_id");

// 分頁參數
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// 查詢資料
$sql = "SELECT * FROM appointment ORDER BY register_time DESC LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

// 生成表格數據
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>#{$row['appointment_id']}</td>
                <td>{$row['sequence_number']}</td>
                <td>{$row['clinic_id']}</td>
                <td>{$row['patient_id']}</td>
                <td>{$row['register_time']}</td>
                <td class='action-icons'>
                    <a href='http://localhost/detail.php?records_id=" . $row['appointment_id'] . "'>🔍</a>
                    <a href='?delete_id={$row['appointment_id']}' onclick='return confirm(\"確定要刪除這筆記錄嗎？\");' class='delete'>🗑️</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>無記錄</td></tr>";
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

$conn->close();
?>
        </tbody>
    </table>
    <div class="pagination">
    </div>
</body>
</html>

