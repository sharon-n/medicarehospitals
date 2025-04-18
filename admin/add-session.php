<?php
session_start();

// Ensure the user is logged in and is an admin
if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
        exit;
    }
} else {
    header("location: ../login.php");
    exit;
}

if ($_POST) {
    // Import database connection
    include("../connection.php");

    // Retrieve and sanitize input values
    $title = mysqli_real_escape_string($database, $_POST["title"]);
    $docid = mysqli_real_escape_string($database, $_POST["docid"]);
    $nop = mysqli_real_escape_string($database, $_POST["nop"]);
    $date = mysqli_real_escape_string($database, $_POST["date"]);
    $start_time = mysqli_real_escape_string($database, $_POST["start_time"]);
    $endtime = mysqli_real_escape_string($database, $_POST["endtime"]);

    // Convert start_time and endtime to DateTime objects
    $startDateTime = new DateTime("$date $start_time");
    $endDateTime = new DateTime("$date $endtime");

    // Calculate the difference
    $interval = $startDateTime->diff($endDateTime);


    // Insert session details into the database
    $sql = "INSERT INTO schedule (docid, title, scheduledate, start_time, endtime, nop) 
            VALUES ('$docid', '$title', '$date', '$start_time', '$endtime', '$nop')";

    if ($database->query($sql) === TRUE) {
        header("Location: schedule.php?action=session-added&title=" . urlencode($title));
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $database->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Session</title>
    <link rel="stylesheet" href="../css/main.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Add New Session</h1>
        <form action="add-session.php" method="post">
            <label for="title">Session Title:</label>
            <input type="text" name="title" required>
            
            <label for="docid">Doctor ID:</label>
            <input type="text" name="docid" required>
            
            <label for="nop">Number of Participants:</label>
            <input type="number" name="nop" required>
            
            <label for="date">Date:</label>
            <input type="date" name="date" required>
            
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" required>
            
            <label for="endtime">End Time:</label>
            <input type="time" name="endtime" required>
            
            <input type="submit" value="Add Session">
        </form>
    </div>
</body>
</html>


