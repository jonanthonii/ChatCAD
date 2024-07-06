<?php

session_start();

require_once '../../config.php';

date_default_timezone_set('Asia/Manila');

if(isset($_SESSION['current_employeeid'])){

    try{

        $current_time = date('Y-m-d H:i:s');
        $username = $_SESSION['current_employeeid'];
    
        $updatestmt = $pdo->prepare("UPDATE users SET last_online = :current_time WHERE employee_id = :username");
        $updatestmt->execute(['current_time' => $current_time, 'username' => $username]);

        // Check if the update was successful
        if($updatestmt->rowCount() > 0) {
            // Return a success message if the update was successful
            echo json_encode(['success' => true]);
        } else {
            // Return an error message if the update failed
            echo json_encode(['error' => 'Failed to update last online status: No rows affected']);
        }

    }catch(PDOException $e){
        // Return an error message if there was a database error
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }

}else{
    echo json_encode(['error' => 'User is not logged in.']);
}

?>