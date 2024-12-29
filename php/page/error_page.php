<?php
require_once '../common/db_conn.php';

function testError($db, $errorType) {
    try {
        switch ($errorType) {
            case 'duplicate_pk':
                // 測試重複的 Primary Key
                $stmt = $db->prepare("
                    INSERT INTO person (person_id, last_name, first_name, id_card, password) 
                    VALUES ('PE00001', '測試', '測試', 'A123456789', 'test')
                ");
                $stmt->execute();
                break;

            case 'invalid_fk':
                // 測試無效的 Foreign Key
                $stmt = $db->prepare("
                    INSERT INTO patient (person_id, height, weight)
                    VALUES ('PE99999', 170, 60)
                ");
                $stmt->execute();
                break;

            case 'delete_parent':
                // 測試刪除被參照的記錄
                $stmt = $db->prepare("
                    DELETE FROM department 
                    WHERE department_id = 'DE00001'
                ");
                $stmt->execute();
                break;

            case 'null_violation':
                // 測試非空欄位插入 NULL
                $stmt = $db->prepare("
                    INSERT INTO person (person_id, last_name, first_name, id_card)
                    VALUES ('PE99999', NULL, NULL, NULL)
                ");
                $stmt->execute();
                break;
        }
    } catch (PDOException $e) {
        return [
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
            'sql_state' => $e->errorInfo[0] ?? 'Unknown',
            'driver_code' => $e->errorInfo[1] ?? 'Unknown',
            'driver_message' => $e->errorInfo[2] ?? 'Unknown'
        ];
    }
    return null;
}

$result = null;
if (isset($_POST['test_type'])) {
    $result = testError($db, $_POST['test_type']);
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>資料庫錯誤測試頁面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .container {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
        }
        .error-box {
            background: #ffe6e6;
            border: 1px solid #ff9999;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .test-button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background: #4CAF50;
            color: white;
        }
        .error-details {
            font-family: monospace;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>資料庫錯誤測試頁面</h1>
        
        <h2>測試案例：</h2>
        <form method="POST">
            <button type="submit" name="test_type" value="duplicate_pk" class="test-button">
                測試重複的 Primary Key
            </button>
            <button type="submit" name="test_type" value="invalid_fk" class="test-button">
                測試無效的 Foreign Key
            </button>
            <button type="submit" name="test_type" value="delete_parent" class="test-button">
                測試刪除被參照的記錄
            </button>
            <button type="submit" name="test_type" value="null_violation" class="test-button">
                測試非空欄位約束
            </button>
        </form>

        <?php if ($result): ?>
        <div class="error-box">
            <h3>錯誤詳情：</h3>
            <div class="error-details">
錯誤代碼：<?php echo htmlspecialchars($result['error_code']); ?>

SQL State：<?php echo htmlspecialchars($result['sql_state']); ?>

驅動程式錯誤代碼：<?php echo htmlspecialchars($result['driver_code']); ?>

錯誤訊息：<?php echo htmlspecialchars($result['driver_message']); ?>

完整錯誤訊息：<?php echo htmlspecialchars($result['error_message']); ?>
            </div>
        </div>
        <?php endif; ?>

        <p><a href="../main/mainpage.php">返回首頁</a></p>
    </div>
</body>
</html>
