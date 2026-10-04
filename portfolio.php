<?php

session_start();
include "db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: signin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email, usn, semester, branch
     FROM students
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Portfolio</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #dfe6e9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 600px;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile {
            font-size: 18px;
            line-height: 2;
        }

        .profile strong {
            display: inline-block;
            width: 120px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 30px;
            padding: 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back:hover {
            background: #0056b3;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>My Portfolio</h1>

    <div class="profile">

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($student['name']); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($student['email']); ?>
        </p>

        <p>
            <strong>USN:</strong>
            <?php echo htmlspecialchars($student['usn']); ?>
        </p>

        <p>
            <strong>Semester:</strong>
            <?php echo htmlspecialchars($student['semester']); ?>
        </p>

        <p>
            <strong>Branch:</strong>
            <?php echo htmlspecialchars($student['branch']); ?>
        </p>

    </div>

    <a href="dashboard.php" class="back">
        Back to Dashboard
    </a>

</div>

</body>

</html>