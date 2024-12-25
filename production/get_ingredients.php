<?php
session_start();
require_once 'config/db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['recipe_id'])) {
    http_response_code(403);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM tbl_ingredients WHERE recipe_id = ?");
    $stmt->execute([$_GET['recipe_id']]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($ingredients);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 