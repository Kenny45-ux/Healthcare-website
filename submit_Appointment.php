<?php
$host = "localhost";
$db = "doctor_portfolio";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['user_name'];
    $email = $_POST['user_email'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $reason = $_POST['reason'];
    // $doctor_id = $_POST['doctor_id']; // If multiple doctors

    $stmt = $conn->prepare("INSERT INTO appointments (user_name, user_email, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $date, $time, $reason);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Appointment requested successfully!'); window.location.href='index.html';</script>";
    } else {
        echo "Something went wrong!";
    }

    $stmt->close();
}

$conn->close();
?>
