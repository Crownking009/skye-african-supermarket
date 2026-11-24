<?php require '../inc/db.php';$e=trim($_POST['email']??'');
if(filter_var($e,FILTER_VALIDATE_EMAIL))db()->prepare('INSERT IGNORE INTO subscribers(email) VALUES(?)')->execute([mb_substr($e,0,160)]);
header('Location: ../index.php?joined=1');
