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
    } catch (PDOException $e) {
        print "===== ERROR ===== " . $e->getMessage();
        die();
    }

    ?>
</body>

</html>