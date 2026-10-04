<?php

include "db.php";

$message = "";

if (isset($_POST['signup'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $usn = trim($_POST['usn']);
    $semester = trim($_POST['semester']);
    $branch = trim($_POST['branch']);

    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($usn) ||
        empty($semester) ||
        empty($branch)
    ) {

        $message = "Please fill all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Enter a valid email address.";

    } else {

        // Check whether email or USN already exists
        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM students WHERE email = ? OR usn = ?"
        );

        mysqli_stmt_bind_param($check, "ss", $email, $usn);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {

            $message = "Email or USN is already registered.";

        } else {

            // Securely encrypt the password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $insert = mysqli_prepare(
                $conn,
                "INSERT INTO students
                (name, email, password, usn, semester, branch)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $insert,
                "ssssss",
                $name,
                $email,
                $hashed_password,
                $usn,
                $semester,
                $branch
            );

            if (mysqli_stmt_execute($insert)) {

                $message = "Registration successful! You can now sign in.";

            } else {

                $message = "Registration failed. Please try again.";
            }
        }

        mysqli_stmt_close($check);
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Sign Up</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #dfe6e9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .box {
            background: white;
            padding: 40px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 14px;
            font-size: 18px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background: #1e7e34;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>Student Sign Up</h2>

    <?php if (!empty($message)) { ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>

    <form method="POST">

        <input
            type="text"
            name="name"
            placeholder="Enter Full Name"
            required
        >

        <input
            type="email"
            name="email"
            placeholder="Enter Email"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Create Password"
            required
        >

        <input
            type="text"
            name="usn"
            placeholder="Enter USN"
            required
        >

        <input
            type="text"
            name="semester"
            placeholder="Enter Semester"
            required
        >

        <input
            type="text"
            name="branch"
            placeholder="Enter Branch"
            required
        >

        <button type="submit" name="signup">
            Register
        </button>

    </form>

</div>

</body>

</html>