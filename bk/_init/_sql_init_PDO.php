<?php

//------------------------------------------------------------------
// データベース初期化
//------------------------------------------------------------------

// *set user_no
$user_no= $_SESSION['user_no'];

try {

    $dbh = new PDO(
        'mysql:host=localhost;dbname=desi_bk;charset=utf8',
        'desi_bk',
        'hahifu',
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        )
    );

} catch (PDOException $e) {

    $error = $e->getMessage();

}

?>
