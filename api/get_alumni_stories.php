<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get approved alumni stories
    $query = "SELECT * FROM alumni_stories WHERE status = 'approved' ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Sanitize output
    foreach ($stories as &$story) {
        $story['alumni_name'] = htmlspecialchars($story['alumni_name']);
        $story['institution'] = htmlspecialchars($story['institution']);
        $story['position'] = htmlspecialchars($story['position']);
        $story['story'] = htmlspecialchars($story['story']);
        $story['job_opportunities'] = htmlspecialchars($story['job_opportunities']);
    }
    
    echo json_encode([
        'success' => true, 
        'stories' => $stories,
        'count' => count($stories)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
