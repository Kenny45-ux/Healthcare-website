<?php
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "doctor_portfolio");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize input
$email = $_POST['email'];
$password = $_POST['password'];

// Check if user exists
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password (if hashed)
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; // ✅ SESSION SET
        header("Location: index.php");      // ✅ Redirect to user dashboard
        exit();
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "No account found with that email.";
}
?>
