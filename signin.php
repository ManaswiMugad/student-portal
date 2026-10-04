<?php

session_start();
include "db.php";

$message = "";

if (isset($_POST['signin'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password, usn, semester, branch
             FROM students
             WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $student = mysqli_fetch_assoc($result);

            if (password_verify($password, $student['password'])) {

                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['name'];
                $_SESSION['student_email'] = $student['email'];

                header("Location: dashboard.php");
                exit();

            } else {

                $message = "Incorrect password.";

            }

        } else {

            $message = "Email not registered.";

        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Sign In</title>

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

        input {
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
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: red;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>Student Sign In</h2>

    <?php if (!empty($message)) { ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>

    <form method="POST">

        <input
            type="email"
            name="email"
            placeholder="Enter Email"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Enter Password"
            required
        >

        <button type="submit" name="signin">
            Sign In
        </button>

    </form>
    <p style="text-align: center; margin-top: 20px;">
    Don't have an account?
    <a href="signup.php">Sign Up</a>
</p>

</div>

</body>

</html>