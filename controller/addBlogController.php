<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'database/Config.php';

$user_id = $_SESSION['id']; // User's ID (stored in session)

$targetDir = "assets/images/";
$fileName = basename($_FILES["image_url"]["name"]);
$targetFilePath = $targetDir . $fileName;
$fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES["image_url"]["error"] == 0) {
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowTypes)) {
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFilePath)) {
            $addBlog = $conn->prepare("INSERT INTO newblogs (blog_title, blog_content, user_id, blog_image) VALUES(?, ?, ?, ?)");
            $addBlog->bind_param('ssis', $_POST['title'], $_POST['content'], $user_id, $fileName);

            if ($addBlog->execute()) {
                $_SESSION['statusMsg'] = "The file " . $fileName . " has been uploaded and blog added successfully.";
            } 
        } else {
            $_SESSION['statusMsg'] = "Error moving uploaded file.";
        }
    } else {
        $_SESSION['statusMsg'] = "Invalid file type: " . $fileType;
    }
} else {
    $_SESSION['statusMsg'] = "File upload error: " . $_FILES["image_url"]["error"];
}

header("Location: add-blog");
exit;
