<?php
session_start();
include "db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: signin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* Get student details */
$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email, usn, semester, branch
     FROM students
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$student_result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($student_result);

mysqli_stmt_close($stmt);

$semester = $student['semester'];

/* Get results */
$stmt = mysqli_prepare(
    $conn,
    "SELECT subject, course_code, credits, marks, grade, grade_point
     FROM results
     WHERE student_id = ? AND semester = ?
     ORDER BY id"
);

mysqli_stmt_bind_param($stmt, "is", $student_id, $semester);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$total_credits = 0;
$total_points = 0;

?>

<!DOCTYPE html>
<html>

<head>

    <title>Final Result</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #dfe6e9;
            margin: 0;
            padding: 30px;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        h2 {
            text-align: center;
            margin-top: 5px;
        }

        .college {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .student-details {
            margin-bottom: 25px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
        }

        .student-details p {
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #007bff;
            color: white;
            padding: 12px;
        }

        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .summary {
            margin-top: 25px;
            display: flex;
            justify-content: space-around;
            gap: 20px;
        }

        .box {
            flex: 1;
            padding: 20px;
            text-align: center;
            background: #e8f4ff;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
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

    <div class="college">
        Student Academic Portfolio
    </div>

    <h1>Final Result</h1>

    <h2>
        Semester <?php echo htmlspecialchars($semester); ?>
    </h2>

    <div class="student-details">

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($student['name']); ?>
        </p>

        <p>
            <strong>USN:</strong>
            <?php echo htmlspecialchars($student['usn']); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($student['email']); ?>
        </p>

        <p>
            <strong>Branch:</strong>
            <?php echo htmlspecialchars($student['branch']); ?>
        </p>

    </div>

    <table>

        <tr>
            <th>Course Title</th>
            <th>Course Code</th>
            <th>Credits</th>
            <th>Marks</th>
            <th>Grade</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <tr>

                <td>
                    <?php echo htmlspecialchars($row['subject']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['course_code']); ?>
                </td>

                <td>
                    <?php echo $row['credits']; ?>
                </td>

                <td>
                    <?php echo $row['marks']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['grade']); ?>
                </td>

            </tr>

            <?php

            $total_credits += $row['credits'];

            $total_points +=
                $row['credits'] * $row['grade_point'];

            ?>

        <?php } ?>

    </table>

    <?php

    if ($total_credits > 0) {
        $sgpa = $total_points / $total_credits;
    } else {
        $sgpa = 0;
    }

    /*
       For now CGPA is based on the current semester.
       Later we will calculate CGPA using all semesters.
    */

    /* Calculate CGPA only from semesters that have results */

$semester_stmt = mysqli_prepare(
    $conn,
    "SELECT semester,
            SUM(credits * grade_point) AS total_points,
            SUM(credits) AS total_credits
     FROM results
     WHERE student_id = ?
     GROUP BY semester"
);

mysqli_stmt_bind_param(
    $semester_stmt,
    "i",
    $student_id
);

mysqli_stmt_execute($semester_stmt);

$semester_result = mysqli_stmt_get_result($semester_stmt);

$total_sgpa = 0;
$semester_count = 0;

while ($sem = mysqli_fetch_assoc($semester_result)) {

    if ($sem['total_credits'] > 0) {

        $semester_sgpa =
            $sem['total_points'] / $sem['total_credits'];

        $total_sgpa += $semester_sgpa;

        $semester_count++;
    }
}

if ($semester_count > 0) {

    $cgpa = $total_sgpa / $semester_count;

} else {

    $cgpa = 0;
}
    ?>

    <div class="summary">

        <div class="box">
            SGPA:
            <?php echo number_format($sgpa, 2); ?>
        </div>

        <div class="box">
            CGPA:
            <?php echo number_format($cgpa, 2); ?>
        </div>

    </div>

    <a href="dashboard.php" class="back">
        Back to Dashboard
    </a>

</div>

</body>

</html>