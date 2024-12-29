<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>醫師登入系統</title>
    <link rel="stylesheet" href="./asset/doctor_login.css">  <!-- 引入外部 CSS 檔案 -->
</head>

<body>
    <div class="login-container">
        <h1>醫師登入</h1>

        <?php
        if (isset($_GET['error'])) {
            echo '<div class="error-message">';
            switch ($_GET['error']) {
                case 'invalid':
                    echo '身分證字號或密碼錯誤';
                    break;
                case 'system':
                    echo '系統錯誤，請稍後再試';
                    break;
            }
            echo '</div>';
        }
        ?>

        <form action="process_doctor_login.php" method="post">
            <label for="id">身分證字號:</label>
            <input type="text" id="id" name="id" required>

            <label for="password">密碼:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">登入</button>
        </form>
        
        <form action="../main/mainpage.php">
            <button type="submit" class="home-btn">返回首頁</button>
        </form>
    </div>
</body>
</html>