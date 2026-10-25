<?php
// Database connection
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_user = getenv('DB_USER') ?: '';
$db_password = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'registration_site';

header('Content-Type: application/json');

if ($db_user === '' || $db_password === '') {
    error_log("Database credentials are not configured via environment variables.");
    die(json_encode(["success" => false, "message" => "Database configuration error."]));
}

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
