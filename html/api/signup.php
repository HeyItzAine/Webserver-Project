<?php
// Database connection
$db_host = '44.209.52.21';  // DB 1 IP
$db_user = 'repl';
$db_password = 'replpassword';
$db_name = 'registration_site';

header('Content-Type: application/json');

// Connect to the database
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$email = trim($_POST['email'] ?? '');
$rawPassword = $_POST['password'] ?? '';

if (!$firstName || !$lastName || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$rawPassword) {
    echo json_encode(["success" => false, "message" => "Invalid signup data"]);
    $conn->close();
    exit;
}

$password = password_hash($rawPassword, PASSWORD_BCRYPT);

// Insert into the database
$sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $firstName, $lastName, $email, $password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Signup successful!"]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
}

// Close connection
$stmt->close();
$conn->close();
?>
