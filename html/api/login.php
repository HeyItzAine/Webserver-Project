<?php
$servername = getenv('DB_HOST') ?: '127.0.0.1';
$username = getenv('DB_USER') ?: '';
$password = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME') ?: 'registration_site';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

header('Content-Type: application/json');

if ($username === '' || $password === '') {
    error_log("Database credentials are not configured via environment variables.");
    die(json_encode(["success" => false, "message" => "Database configuration error."]));
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$pass = $data['password'] ?? '';

$isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isSecure,
    'samesite' => 'Lax',
]);
session_start(); // Start the session

// Check credentials
$sql = "SELECT first_name, last_name, email, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Use password_verify to compare the raw password with the hashed password
    if (password_verify($pass, $user['password'])) {
	// After successful login:
	// $_SESSION['username'] = $user['email']; // You can also use a different identifier like $user['first_name'] or $user['id']
	$_SESSION['user'] = [
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'email' => $user['email'],
        ];
        session_regenerate_id(true);

        // Return redirect URL for the frontend
        echo json_encode([
            "success" => true,
            "message" => "Login successful!",
	    "redirect" => "/dashboard.php",
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}

$stmt->close();
$conn->close();
?>
