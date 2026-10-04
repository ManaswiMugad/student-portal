<?php

session_start();
include "db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: signin.php");
    exit();
}

$message = "";

if (isset($_POST['add_result'])) {

    $student_id = $_SESSION['student_id'];

    $semester = trim($_POST['semester']);
    $subject = trim($_POST['subject']);
    $course_code = trim($_POST['course_code']);
    $credits = intval($_POST['credits']);
    $marks = intval($_POST['marks']);

    /* Check whether all fields are valid */

    if (
        empty($semester) ||
        empty($subject) ||
        empty($course_code) ||
        $credits <= 0 ||
        $marks < 0 ||
        $marks > 100
    ) {

        $message = "Please fill all fields correctly.";

    } else {

        /* Check how many subjects already exist
           for this student and semester */

        $count_stmt = mysqli_prepare(
            $conn,
            "SELECT COUNT(*) AS subject_count
             FROM results
             WHERE student_id = ? AND semester = ?"
        );

        mysqli_stmt_bind_param(
            $count_stmt,
            "is",
            $student_id,
            $semester
        );

        mysqli_stmt_execute($count_stmt);

        $count_result = mysqli_stmt_get_result($count_stmt);

        $count_data = mysqli_fetch_assoc($count_result);

        $subject_count = $count_data['subject_count'];

        mysqli_stmt_close($count_stmt);


        /* Maximum 6 subjects per semester */

        if ($subject_count >= 6) {

            $message =
                "This semester already has 6 subjects. You cannot add another subject.";

        } else {

            /* Calculate grade and grade point */

            if ($marks >= 90) {

                $grade = "O";
                $grade_point = 10;

            } elseif ($marks >= 80) {

                $grade = "A+";
                $grade_point = 9;

            } elseif ($marks >= 70) {

                $grade = "A";
                $grade_point = 8;

            } elseif ($marks >= 60) {

                $grade = "B+";
                $grade_point = 7;

            } elseif ($marks >= 50) {

                $grade = "B";
                $grade_point = 6;

            } elseif ($marks >= 40) {

                $grade = "C";
                $grade_point = 5;

            } else {

                $grade = "F";
                $grade_point = 0;
            }


            /* Insert result */

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO results
                (
                    student_id,
                    semester,
                    subject,
                    course_code,
                    credits,
                    marks,
                    grade,
                    grade_point
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "isssiisd",
                $student_id,
                $semester,
                $subject,
                $course_code,
                $credits,
                $marks,
                $grade,
                $grade_point
            );


            if (mysqli_stmt_execute($stmt)) {

                $new_count = $subject_count + 1;

                if ($new_count == 6) {

                    $message =
                        "Result added successfully! This semester now has all 6 subjects.";

                } else {

                    $remaining = 6 - $new_count;

                    $message =
                        "Result added successfully! "
                        . $remaining
                        . " subject(s) remaining for this semester.";
                }

            } else {

                $message = "Failed to add result.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Result</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #dfe6e9;
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            color: #555;
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input,
        select {
            display: block;
            width: 100%;
            height: 45px;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            background: white;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #007bff;
        }

        button {
            display: block;
            width: 100%;
            height: 45px;
            margin-top: 10px;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            background: #e8f4ff;
            text-align: center;
            border-radius: 6px;
        }

        .back {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #007bff;
        }

        .back:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Add Result</h1>

    <p class="info">
        Each semester can have a maximum of 6 subjects.
        Grade and Grade Point are calculated automatically from marks.
    </p>


    <?php if (!empty($message)) { ?>

        <div class="message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <form method="POST">

        <div class="form-group">

            <label>Semester</label>

            <select name="semester" required>

                <option value="">Select Semester</option>

                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
                <option value="3">Semester 3</option>
                <option value="4">Semester 4</option>
                <option value="5">Semester 5</option>
                <option value="6">Semester 6</option>
                <option value="7">Semester 7</option>
                <option value="8">Semester 8</option>

            </select>

        </div>


        <div class="form-group">

            <label>Subject Name</label>

            <input
                type="text"
                name="subject"
                placeholder="Example: Machine Learning"
                required
            >

        </div>


        <div class="form-group">

            <label>Course Code</label>

            <input
                type="text"
                name="course_code"
                placeholder="Example: 22UCSC601"
                required
            >

        </div>


        <div class="form-group">

            <label>Credits</label>

            <input
                type="number"
                name="credits"
                min="1"
                placeholder="Example: 3"
                required
            >

        </div>


        <div class="form-group">

            <label>Marks</label>

            <input
                type="number"
                name="marks"
                min="0"
                max="100"
                placeholder="Enter marks out of 100"
                required
            >

        </div>


        <button type="submit" name="add_result">

            Add Result

        </button>

    </form>


    <a href="dashboard.php" class="back">

        Back to Dashboard

    </a>

</div>

</body>

</html>