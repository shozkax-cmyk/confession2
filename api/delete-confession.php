<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';
require_once '../functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$confessionId = intval($input['confession_id'] ?? 0);

if ($confessionId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid confession ID']);
    exit;
}

try {
    // Check if user owns the confession or is admin
    $stmt = $pdo->prepare("SELECT user_id, image FROM confessions WHERE id = ?");
    $stmt->execute([$confessionId]);
    $confession = $stmt->fetch();
    
    if (!$confession) {
        http_response_code(404);
        echo json_encode(['error' => 'Confession not found']);
        exit;
    }
    
    // Check permissions
    if (!isAdmin() && $confession['user_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Permission denied']);
        exit;
    }
    
    // Delete the confession
    if ($db->deleteConfession($confessionId)) {
        // Delete associated image if exists
        if ($confession['image'] && file_exists('uploads/' . $confession['image'])) {
            unlink('uploads/' . $confession['image']);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Confession deleted successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete confession']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>