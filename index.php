<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Selection</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Data Selection</h1>
    <form action="report.php" method="post">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        <br>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
        <br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
