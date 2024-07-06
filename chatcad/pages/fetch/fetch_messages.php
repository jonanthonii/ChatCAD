<?php

require '../../config.php'; // Make sure to include your database connection

try {
    $countstmt = $pdo->prepare("SELECT COUNT(*) AS total_count FROM conversation");
    $countstmt->execute();
    $count = $countstmt->fetch(PDO::FETCH_ASSOC)['total_count'];
    $offset = max(0, $count - 1000);

    $stmt = $pdo->prepare("SELECT c.*, u.*, CONCAT(u.firstname, ' ', u.lastname) AS fullname FROM conversation c INNER JOIN users u ON c.sender_id = u.employee_id ORDER BY c.sent_date ASC LIMIT 1000 OFFSET :offset");
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
