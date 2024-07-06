<?php

include '../../config.php';

try{
    $timelimit = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY last_online DESC");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
    
}catch(PDOException $e){
    echo json_encode(['error'=> $e->getMessage()]);
}

?>