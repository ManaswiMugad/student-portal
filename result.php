<?php

session_start();
include "db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: signin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* Get all semesters that have results */

$semester_stmt = mysqli_prepare(
    $conn,
    "SELECT DISTINCT semester
     FROM results
     WHERE student_id = ?
     ORDER BY CAST(semester AS UNSIGNED)"
);

mysqli_stmt_bind_param(
    $semester_stmt,
    "i",
    $student_id
);

mysqli_stmt_execute($semester_stmt);

$semester_result = mysqli_stmt_get_result($semester_stmt);


/* Variables for overall CGPA */

$overall_points = 0;
$overall_credits = 0;

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Results</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #dfe6e9;
            margin: 0;
            padding: 30px 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .semester-box {
            margin-bottom: 35px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        .semester-title {
            background: #007bff;
            color: white;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f1f1f1;
            padding: 12px;
            border: 1px solid #ddd;
        }

        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background: #fafafa;
        }

        .semester-summary {
            padding: 18px;
            background: #e8f4ff;
            text-align: center;
        }

        .semester-summary span {
            margin: 0 20px;
            font-weight: bold;
        }

        .pass {
            color: green;
            font-weight: bold;
        }

        .fail {
            color: red;
            font-weight: bold;
        }

        .overall {
            margin-top: 30px;
            padding: 25px;
            background: #f5f5f5;
            border-radius: 10px;
            text-align: center;
        }

        .overall h2 {
            margin-top: 0;
        }

        .cgpa {
            font-size: 25px;
            font-weight: bold;
            margin: 15px;
        }

        .button {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .button:hover {
            background: #0056b3;
        }

        .no-result {
            text-align: center;
            padding: 30px;
            color: #555;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>My Academic Results</h1>


    <?php

    if (mysqli_num_rows($semester_result) == 0) {

    ?>

        <div class="no-result">

            No results have been added yet.

        </div>

    <?php

    }


    while ($semester_row = mysqli_fetch_assoc($semester_result)) {

        $semester = $semester_row['semester'];


        /* Get subjects for this semester */

        $result_stmt = mysqli_prepare(
            $conn,
            "SELECT subject,
                    course_code,
                    credits,
                    marks,
                    grade,
                    grade_point
             FROM results
             WHERE student_id = ?
             AND semester = ?
             ORDER BY id"
        );

        mysqli_stmt_bind_param(
            $result_stmt,
            "is",
            $student_id,
            $semester
        );

        mysqli_stmt_execute($result_stmt);

        $result = mysqli_stmt_get_result($result_stmt);


        $semester_credits = 0;
        $semester_points = 0;
        $semester_failed = false;

    ?>


        <div class="semester-box">

            <div class="semester-title">

                Semester <?php echo htmlspecialchars($semester); ?>

            </div>


            <table>

                <tr>

                    <th>Subject</th>
                    <th>Course Code</th>
                    <th>Credits</th>
                    <th>Marks</th>
                    <th>Grade</th>
                    <th>Grade Point</th>

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

                        <td>
                            <?php echo $row['grade_point']; ?>
                        </td>

                    </tr>


                    <?php

                    $semester_credits += $row['credits'];

                    $semester_points +=
                        $row['credits'] * $row['grade_point'];


                    if ($row['grade'] == "F") {

                        $semester_failed = true;

                    }


                    $overall_credits += $row['credits'];

                    $overall_points +=
                        $row['credits'] * $row['grade_point'];

                    ?>

                <?php } ?>

            </table>


            <?php

            if ($semester_credits > 0) {

                $semester_sgpa =
                    $semester_points / $semester_credits;

            } else {

                $semester_sgpa = 0;

            }


            if ($semester_failed) {

                $semester_status = "FAIL";

            } else {

                $semester_status = "PASS";

            }

            ?>


            <div class="semester-summary">

                <span>
                    SGPA:
                    <?php echo number_format($semester_sgpa, 2); ?>
                </span>


                <span class="<?php
                    echo ($semester_status == "PASS")
                        ? "pass"
                        : "fail";
                ?>">

                    Result:
                    <?php echo $semester_status; ?>

                </span>

            </div>

        </div>


        <?php

        mysqli_stmt_close($result_stmt);

    }


    /* Overall CGPA */

    if ($overall_credits > 0) {

        $cgpa =
            $overall_points / $overall_credits;

    } else {

        $cgpa = 0;

    }


    /* Overall PASS / FAIL */

    $overall_status = "PASS";


    $fail_stmt = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS fail_count
         FROM results
         WHERE student_id = ?
         AND grade = 'F'"
    );

    mysqli_stmt_bind_param(
        $fail_stmt,
        "i",
        $student_id
    );

    mysqli_stmt_execute($fail_stmt);

    $fail_result = mysqli_stmt_get_result($fail_stmt);

    $fail_data = mysqli_fetch_assoc($fail_result);


    if ($fail_data['fail_count'] > 0) {

        $overall_status = "FAIL";

    }

    mysqli_stmt_close($fail_stmt);

    ?>


    <div class="overall">

        <h2>Overall Academic Result</h2>

        <div class="cgpa">

            CGPA:
            <?php echo number_format($cgpa, 2); ?>

        </div>


        <div class="<?php
            echo ($overall_status == "PASS")
                ? "pass"
                : "fail";
        ?>">

            Overall Result:
            <?php echo $overall_status; ?>

        </div>

    </div>


    <div style="text-align:center;">

        <a href="dashboard.php" class="button">
            Back to Dashboard
        </a>

        <a href="add_result.php" class="button">
            Add Result
        </a>

    </div>

</div>

</body>

</html>