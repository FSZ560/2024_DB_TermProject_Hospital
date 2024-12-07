<html>

<head>
</head>

<body>
    <?php

    $user = 'root';
    $password = 'DB_team_11_password';

    try {
        $db = new PDO('mysql:host=localhost;dbname=db_team_11_project;charset=utf8', $user, $password);

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        echo "DB Connection Success";
        echo "<br>";
        date_default_timezone_set('Asia/Taipei');
        echo "now time is " . date("Y-m-d H:i:s");
    } catch (PDOException $e) {
        print "===== ERROR ===== " . $e->getMessage();
        die();
    }

    ?>
</body>

</html>