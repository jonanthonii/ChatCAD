<?php

include 'config.php';
session_start();
date_default_timezone_set('Asia/Manila');

if(isset($_SESSION['current_employeeid'])){

    $id = $_SESSION['current_employeeid'];

    $current_time = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("UPDATE users SET last_online = :last_online WHERE employee_id = :id");
    $stmt->execute([':last_online' => $current_time, ':id' => $id]);
    
    session_unset();
    session_destroy();
    header('Location: index.php?success=true');
}else{
    header('Location: index.php?success=true');
}

?>