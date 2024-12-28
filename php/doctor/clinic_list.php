<?php
session_start();
require_once '../common/db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: doctor_login.php");
    exit();
}

try {
    $stmt = $db->prepare("
        SELECT c.clinic_id, c.clinic_date, c.period, c.location, 
               d.department_name, 
               COUNT(DISTINCT a.appointment_id) as appointment_count,
               COUNT(DISTINCT mr.record_id) as treated_count
        FROM clinic c
        JOIN department d ON c.department_id = d.department_id
        LEFT JOIN appointment a ON c.clinic_id = a.clinic_id
        LEFT JOIN medical_record mr ON c.clinic_id = mr.clinic_id
        WHERE c.doctor_id = ?
        GROUP BY c.clinic_id
        ORDER BY c.clinic_date DESC, 
                 FIELD(c.period, 'morning', 'afternoon', 'evening')
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $clinics = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "系統錯誤，請稍後再試";
}

function getPeriodText($period) {
    switch ($period) {
        case 'morning':
            return '上午';
        case 'afternoon':
            return '下午';
        case 'evening':
            return '晚上';
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>門診清單</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #ff9900;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #ff9900 !important;
            color: white;
            cursor: pointer;
            position: relative; /* 讓圖示可以定位 */
            padding-right: 30px; /* 讓右側有更多空間顯示圖示 */
        }

        th .sort-icon {
            position: absolute;
            right: 10px; /* 讓圖示位於表格右側 */
            top: 50%; /* 垂直居中 */
            transform: translateY(-50%); /* 精確垂直居中 */
            font-size: 16px;
            z-index: 1; /* 確保圖示位於表格標題文字上方 */
        }

        th:nth-child(1), td:nth-child(1) {
            width: 15%; /* 第一列寬度為 20% */
        }

        th:nth-child(2), td:nth-child(2) {
            width: 7%; /* 第二列寬度為 10% */
        }

        th:nth-child(3), td:nth-child(3) {
            width: 7%; /* 第三列寬度為 15% */
        }

        th:nth-child(4), td:nth-child(4) {
            width: 20%; /* 第四列寬度為 25% */
        }

        th:nth-child(5), td:nth-child(5) {
            width: 13%; /* 第五列寬度為 10% */
        }

        th:nth-child(6), td:nth-child(6) {
            width: 13%; /* 第六列寬度為 10% */
        }

        th:nth-child(7), td:nth-child(7) {
            width: 25%; /* 第七列寬度為 10% */
        }


        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .button-group {
            margin-top: 20px;
        }

        .back-btn {
            padding: 10px 20px;
            background-color: #666;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .error {
            color: red;
            background-color: #ffe6e6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            margin: 0 5px;
            display: inline-block;
            font-size: 14px;
        }

        .consult-btn {
            background-color: #4CAF50;
        }

        .consult-btn:hover {
            background-color: #45a049;
        }

        .list-btn {
            background-color: #2196F3;
        }

        .list-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>門診清單</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (empty($clinics)): ?>
            <div class="empty-message">目前沒有門診記錄</div>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th data-sort="clinic_date">門診日期</th>
                        <th data-sort="period">時段</th>
                        <th data-sort="department_name">科別</th>
                        <th data-sort="location">看診地點</th>
                        <th data-sort="appointment_count">待看診人數</th>
                        <th data-sort="treated_count">已看診人數</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clinics as $clinic): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($clinic['clinic_date']); ?></td>
                            <td><?php echo htmlspecialchars(getPeriodText($clinic['period'])); ?></td>
                            <td><?php echo htmlspecialchars($clinic['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['location']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['appointment_count']); ?></td>
                            <td><?php echo htmlspecialchars($clinic['treated_count']); ?></td>
                            <td>
                                <a href="patient_consult.php?clinic_id=<?php echo urlencode($clinic['clinic_id']); ?>" class="action-btn consult-btn">進行看診</a>
                                <a href="patient_list.php?clinic_id=<?php echo urlencode($clinic['clinic_id']); ?>" class="action-btn list-btn">已看病人清單</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="button-group">
            <a href="doctor_dashboard.php" class="back-btn">返回</a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // 預設
            $('th').each(function() {
                $(this).addClass('descending');  // 預設為降冪
            });
            
            // 自動顯示第一列的 down 圖示
            var firstColumn = $('th').eq(0); // 第一列
            if (firstColumn.index() !== $('th').length - 1) {  // 忽略最後一欄
                firstColumn.addClass('descending'); // 設置為降冪排序
                firstColumn.append('<span class="sort-icon"><i class="bx bx-sort-down"></i></span>'); // 顯示圖示
            }
            firstColumn.append('<span class="sort-icon"><i class="bx bx-sort-down"></i></span>'); // 顯示圖示

            // 點擊排序功能
            firstColumn.trigger('click'); // 模擬第一次點擊排序

            $('th').click(function() {
                var index = $(this).index();

                if (index !== $('th').length - 1) {
                var rows = $('tbody tr').toArray();
                var isAscending = $(this).hasClass('descending');
                
                // 先移除所有表頭的圖示
                $('th').find('.sort-icon').remove();

                // 根據排序方向添加相應的圖示
                var icon = isAscending ? '<i class="bx bx-sort-down"></i>' : '<i class="bx bx-sort-up"></i>';
                $(this).append('<span class="sort-icon">' + icon + '</span>'); // 在當前點擊的表頭中顯示排序圖示

                // 排序表格
                rows.sort(function(a, b) {
                    var cellA = $(a).children('td').eq(index).text();
                    var cellB = $(b).children('td').eq(index).text();
                    if ($.isNumeric(cellA) && $.isNumeric(cellB)) {
                        return cellA - cellB;
                    }
                    return cellA.localeCompare(cellB);
                });

                // 如果是升冪，反轉行數據
                if (isAscending) {
                    rows.reverse();
                }

                // 移除所有的 ascending 和 descending 樣式
                $('th').removeClass('ascending descending');

                // 設置當前列的排序方向樣式
                if (isAscending) {
                    $(this).addClass('ascending');
                } else {
                    $(this).addClass('descending');
                }

                // 更新表格內容
                $('tbody').append(rows);
                }
            });
        });
    </script>
</body>
</html>
