<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $event_name = $_POST['event_name'];

    $stmt = $conn->prepare("INSERT INTO bookings (name, email, event_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $event_name);

    if ($stmt->execute()) {
        echo "<h2>Booking Successful!</h2>";
        echo "<p><a href='index.html'>Back to Home</a></p>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
