<?php
// functions.php
function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function check_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit;
    }
}


function get_user_orders($user_id, $pdo) {
    $stmt = $pdo->prepare("
        SELECT o.*, e.name as equipment_name, p.address 
        FROM orders o
        JOIN equipment e ON o.equipment_id = e.equipment_id
        JOIN pickup_points p ON o.point_id = p.point_id
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_all_orders($pdo) {
    $stmt = $pdo->prepare("
        SELECT o.*, u.full_name, u.phone, u.email, e.name as equipment_name, p.address 
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN equipment e ON o.equipment_id = e.equipment_id
        JOIN pickup_points p ON o.point_id = p.point_id
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>