<?php

session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: signin.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Dashboard</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #dfe6e9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 700px;
            margin: 80px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
            text-align: center;
        }

        h1 {
            margin-bottom: 10px;
        }

        .welcome {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .button {
            display: inline-block;
            padding: 14px 25px;
            margin: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background: #0056b3;
        }

        .logout {
            background: #dc3545;
        }

        .logout:hover {
            background: #a71d2a;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Student Dashboard</h1>

    <div class="welcome">
        Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?>!
    </div>

    <a href="portfolio.php" class="button">My Portfolio</a>

<a href="result.php" class="button">My Results</a>
<a href="add_result.php" class="button">Add Result</a>

<a href="finalresult.php" class="button">Final Result</a>

<br>

<a href="logout.php" class="button logout">Logout</a>

</div>

</body>

</html>