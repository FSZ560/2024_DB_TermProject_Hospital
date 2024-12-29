<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>病患登入系統</title>
    <link rel="stylesheet" href="asset/patient_login.css">
</head>

<body>
    <div class="login-container">
        <h1>病患登入</h1>

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

        <form action="process_patient_login.php" method="post">
            <label for="id">身分證字號:</label>
            <input type="text" id="id" name="id" required>

            <label for="password">密碼:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">登入</button>
        </form>

        <form action="patient_register.php">
            <button type="submit" class="register-btn" style="margin-bottom: 10px;">註冊個人資料</button>
        </form>
        
        <form action="../main/mainpage.php">
            <button type="submit" class="home-btn">返回首頁</button>
        </form>
    </div>
</body>

</html>
