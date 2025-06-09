<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

requireAdmin();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Validate required fields
    $required_fields = ['level', 'position', 'competencies', 'certifications', 'description'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Field $field is required"]);
            exit();
        }
    }
    
    // Handle image upload
    $image_filename = null;
    if (isset($_FILES['career_image']) && $_FILES['career_image']['error'] === UPLOAD_ERR_OK) {
        $image_filename = uploadImage($_FILES['career_image'], 'uploads/careers/');
        if (!$image_filename) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    }
    
    // Insert career data
    $query = "INSERT INTO career_roadmap (level, position, competencies, certifications, description, salary_range, image) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        sanitizeInput($_POST['level']),
        sanitizeInput($_POST['position']),
        sanitizeInput($_POST['competencies']),
        sanitizeInput($_POST['certifications']),
        sanitizeInput($_POST['description']),
        sanitizeInput($_POST['salary_range']),
        $image_filename
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Career added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add career']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
