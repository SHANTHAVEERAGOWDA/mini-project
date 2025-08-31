<?php
require 'db.php';

$result = $conn->query("SELECT * FROM bookings ORDER BY booking_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Bookings</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>All Bookings</h1>
    <table border="1" width="100%" cellpadding="5">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Event</th>
        <th>Booked At</th>
      </tr>

      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= $row['name'] ?></td>
          <td><?= $row['email'] ?></td>
          <td><?= $row['event_name'] ?></td>
          <td><?= $row['booking_time'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
    <br>
    <a href="index.html">Back to Home</a>
  </div>
</body>
</html>
