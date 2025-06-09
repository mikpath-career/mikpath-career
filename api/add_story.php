<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Validate required fields
    $required_fields = ['alumni_name', 'graduation_year', 'institution', 'position', 'story'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Field $field is required"]);
            exit();
        }
    }
    
    // Handle image upload
    $image_filename = null;
    if (isset($_FILES['alumni_image']) && $_FILES['alumni_image']['error'] === UPLOAD_ERR_OK) {
        $image_filename = uploadImage($_FILES['alumni_image'], 'uploads/alumni/');
        if (!$image_filename) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    }
    
    // Insert story data
    $query = "INSERT INTO alumni_stories (alumni_name, graduation_year, institution, position, story, job_opportunities, image, is_anonymous, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'approved')";
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        sanitizeInput($_POST['alumni_name']),
        (int)$_POST['graduation_year'],
        sanitizeInput($_POST['institution']),
        sanitizeInput($_POST['position']),
        sanitizeInput($_POST['story']),
        sanitizeInput($_POST['job_opportunities']),
        $image_filename,
        isset($_POST['is_anonymous']) ? 1 : 0
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cerita berhasil dikirim dan langsung ditampilkan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit story']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
