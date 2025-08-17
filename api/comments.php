<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';
require_once '../functions.php';

// Handle GET request - fetch comments for a confession
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $confessionId = intval($_GET['confession_id'] ?? 0);
    
    if ($confessionId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid confession ID']);
        exit;
    }
    
    try {
        $comments = $db->getCommentsByConfessionId($confessionId);
        
        // Format comments for display
        $formattedComments = array_map(function($comment) {
            return [
                'id' => $comment['id'],
                'username' => htmlspecialchars($comment['username']),
                'text' => htmlspecialchars($comment['text']),
                'date' => $comment['date'],
                'image' => $comment['image'],
                'style' => $comment['style'] ? json_decode($comment['style'], true) : null
            ];
        }, $comments);
        
        echo json_encode([
            'success' => true,
            'comments' => $formattedComments
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch comments']);
    }
    
    exit;
}

// Handle POST request - add new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    
    $confessionId = intval($_POST['confession_id'] ?? 0);
    $text = trim($_POST['comment_text'] ?? '');
    
    if ($confessionId <= 0 || empty($text)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }
    
    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['comment_image']) && $_FILES['comment_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleImageUpload($_FILES['comment_image']);
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['filename'];
        } else {
            http_response_code(400);
            echo json_encode(['error' => $uploadResult['message']]);
            exit;
        }
    }
    
    try {
        $success = $db->createComment($_SESSION['user_id'], $confessionId, $text, null, $imagePath);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Comment added successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add comment']);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
    
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
?>