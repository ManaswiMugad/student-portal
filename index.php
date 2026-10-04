<!DOCTYPE html>
<html>
<head>
    <title> Student Portfolio</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #dfe6e9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 40px;
            width: 400px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
        }

        h1 {
            margin-bottom: 30px;
        }

        button {
            padding: 15px 35px;
            margin: 10px;
            font-size: 18px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Student Portfolio</h1>

    <a href="signin.php">
        <button>Sign In</button>
    </a>

    <a href="signup.php">
        <button>Sign Up</button>
    </a>

</div>

</body>
</html>