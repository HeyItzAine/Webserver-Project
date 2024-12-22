<?php
$servername = "44.209.52.21";  // DB 1 IP
$username = "repl";
$password = "replpassword";  // Change this to your actual password
$dbname = "registration_site";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$pass = $data['password'] ?? '';

// Prevent SQL injection
$email = $conn->real_escape_string($email);
$pass = $conn->real_escape_string($pass);

session_start(); // Start the session

// Check credentials
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

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

$conn->close();
?>
