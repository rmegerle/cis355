<?php
session_start();
$hostname = "localhost";
$username = "CIS355rtmegerl";
$password = "cis355";
$dbname = "CIS355rtmegerl";
$usertable = "quizzes";


# ---------- showQuizzes ---------------------------------------------------------
// this function gets records from a "mysql table" and builds an "html table"

function showQuizzes($mysqli, $Created = -1) {
    if ($Created != -1) {
        $_SESSION['LessonID'] = $Created;
    }

    //HEY, add a form here if you want more buttons on the top
    echo '<div class="col-md-12">
        <form action="Lessons.php" method="POST">
                <input type="submit" name="BackToLessons" value="Back to Lessons" class="btn btn-primary""> </form> <br>
        <form action="Quizzes.php" method="POST">
        <table class="table table-condensed" 
        style="border: 1px solid #dddddd; border-radius: 5px; 
        box-shadow: 2px 2px 10px;">
        <tr><td colspan="11" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;">
        <h2 style="color: white;">Quizzes</h2>
        </td></tr><tr style="font-weight:800; font-size:20px;">
        <td>ID</td><td>Lesson\'s Title</td><td>Lesson\'s Subject</td>
        <td>Max Attempts</td> <td>Quiz Title</td><td>Quiz Description</td></tr>';

// get count of records in mysql table
    $countresult = $mysqli->query("SELECT COUNT(*) FROM quizzes WHERE lessons_id= " . $_SESSION['LessonID']);
    $countfetch = $countresult->fetch_row();
    $countvalue = $countfetch[0];
    $countresult->close();

// if records > 0 in mysql table, then populate html table, 
// else display "no records" message
    if ($countvalue > 0) {
        populateQuizzes($mysqli); // populate html table, from mysql table
    } else {
        echo '<p>No records in database table</p><br>';
    }

// display html buttons 
    echo '</table>
        <input type="hidden" id="hid" name="hid" value="">
        <input type="hidden" id="uid" name="uid" value="">
        <input type="submit" name="InsertAQuizzes" value="Add an Entry" class="btn btn-primary""></form>';

    echo "<script>
        function setHid(num){
            document.getElementById('hid').value = num;
	}
	function setUid(num) {
            document.getElementById('uid').value = num;
	}
	</script>";
}

# ---------- populateQuizzes ----------------------------------------------------
// populate html table, from data in mysql table

function populateQuizzes($mysqli) {
    global $usertable;
    $Lesson = $_SESSION['LessonID'];

    if ($result = $mysqli->query("SELECT quizzes.id, lessons.title, lessons.subject, attempts_allowed, "
            . "quizzes.title, quizzes.description FROM lessons LEFT JOIN quizzes ON lessons.id=quizzes.lessons_id "
            . "where quizzes.lessons_id =$Lesson")) {

        while ($row = $result->fetch_row()) {
            echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' .
            $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] .
            '</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' .
            $row[7] . '</td><td>' . $row[8] . '</td><td>' . $row[9];

            echo '<input type="submit" name="SelectAQuizzes" class="btn btn-primary" value="Select" 
				onclick="setUid(' . $row[0] . ');" />';
            if (AlreadyReviewed($mysqli, $row[0]) == 0) {
            echo '<input type="submit" input style="margin-left: 10px" name="WriteAReview" class="btn btn-primary" value="Write Review"  onclick="setUid(' . $row[0] . ');" />';
            } else {
                echo '<input type="submit" input style="margin-left: 10px" name="PreviousReview" class="btn btn-primary" value="Previous Review"  onclick="setUid(' . $row[0] . ');" />';
            }
            if ($_SESSION['PersonsRole'] == "Teacher" || $_SESSION['PersonsRole'] == "Peer Reviewer" ||
                    $_SESSION['SecRole'] == "Teacher" || $_SESSION['SecRole'] == "Peer Reviewer") {
                echo '</td><td><input name="DeleteAQuizzes" type="submit" 
				class="btn btn-danger" value="Delete" onclick="setHid(' .
                $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit" 
				name="UpdateAQuizzes" class="btn btn-primary" value="Update" 
				onclick="setUid(' . $row[0] . ');" />';
            }
        }
    }
    $result->close();
}

function AlreadyReviewed($mysqli, $QuizID) {
    // get count of records in mysql table
    $countresult = $mysqli->query("SELECT Count(*) FROM `quizReview` WHERE `persons_id` = " . $_SESSION['PersonID']
            . " and `quizzes_id` = " . $QuizID);
    $countfetch = $countresult->fetch_row();
    $countresult->close();
    return $countfetch[0];
}

function deleteQuizzesRecord($mysqli) {
    $index = $_SESSION['QuizID'];  // "hid" is id of db record to be deleted
    global $usertable;

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("DELETE FROM $usertable WHERE id='$index'")) {
        $stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
}

function ShowQuizzesUpdateForm($mysqli) {
    $index = $_SESSION['QuizID'];  // "uid" is id of db record to be updated 
    global $usertable;
    if ($result = $mysqli->query("SELECT id, attempts_allowed, title, description FROM $usertable WHERE id = $index")) {
        while ($row = $result->fetch_row()) {
            echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Quizzes.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Update Quiz</h2></td></tr>';


            echo '<tr><td>Max Attempts: </td><td><input type="number" name="max_attempts" value= ' . $row[1] . ' min="1" max="100000"></td></tr>
             <tr><td>Title of Quiz: </td><td><input type="edit" name="quiz_title" value= "' . $row[2] . ' " size="49"></td></tr>
             <tr><td>Quiz Description: </td><td><textarea maxlength="200" style="resize: none;" name="quiz_des" cols="51" rows="3">' . $row[3] . ' </textarea></td></tr>';

            echo '
        </td></tr> 
        <tr><td><input type="submit" name="QuizzesExecuteUpdate" class="btn btn-primary" value="Update Entry"></td> 
	</table> <input type="hidden" name="uid" value="' . $row[0] . '"> </form> 
        <form action="Quizzes.php"> <input type="submit" name="BackToQuizzes" value="Back to Quizzes" class="btn btn-primary""> </form> <br> </div>';
        }
        $result->close();
    }
}

function showQuizzesInsertForm($mysqli) {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Quizzes.php" 
        onSubmit="return validate();">
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;">
        <h2>Create New Quiz</h2></td></tr>';

    InsertLessonFieldsCombobox($mysqli);

    echo '<tr><td>Max Attempts: </td><td><input type="number" name="max_attempts" min="1" max="100000"></td></tr>
             <tr><td>Title of Quiz: </td><td><input type="edit" name="quiz_title" value="" size="49"></td></tr>
             <tr><td>Quiz Description: </td><td><textarea maxlength="200" style="resize: none;" name="quiz_des" cols="51" rows="3"></textarea></td></tr>';

    echo '<tr><td><input type="submit" name="QuizzesExecuteInsert" class="btn btn-success" value="Add Entry"></td> <td style="text-align: right;">
        <input type="reset" class="btn btn-danger" name="UpdateAQuizzes" onclick="history.go(-1);" value="Exit"></td></tr> </table>
        <a href="Quizzes.php" class="btn btn-primary">Display Quizzes</a></form></div>';
}

function InsertLessonFieldsCombobox($mysqli) {
    if ($result = $mysqli->query("SELECT id, title, subject FROM lessons WHERE persons_id = " . $_SESSION['PersonID'] . ' AND id = ' . $_SESSION['LessonID'])) {
        $row = $result->fetch_row();
        echo '<tr><td>' . "Select Lesson: " . ': </td><td> <input type="edit" name="quiz_title" value= "'
        . $row[0] . " - " . $row[1] . " - " . $row[2] . '" size="49" readonly></td></tr>';
    }
}

function CreateQuiz($mysqli) {
    global $usertable;
    $lesson_id = $_SESSION['LessonID'];
    $max_attempts = $_POST['max_attempts'];
    $title = $_POST['quiz_title'];
    $description = $_POST['quiz_des'];

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("INSERT INTO $usertable (id, lessons_id, attempts_allowed, 
                                    title, description) VALUES ('NULL', '$lesson_id', '$max_attempts', '$title', 
                                    '$description')")) {
        $stmt->execute();
        $stmt->close();
    }
    $_SESSION['LessonID'] = $lesson_id;
}

function updateQuizzes($mysqli) {
    global $usertable;

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare('UPDATE ' . $usertable . ' SET  `attempts_allowed` =  ' . $_POST['max_attempts'] . ',
                `title` =  ' . "'" . $_POST['quiz_title'] . "'" . ',
                `description` =  ' . "'" . $_POST['quiz_des'] . "'" . ' WHERE  id =' . $_SESSION['QuizID'])) {
        $stmt->execute();
        $stmt->close();
    }
}

function GetSelectedValue($mysqli) {
    $item = 0;
    if ($result = $mysqli->query("SELECT id, title, subject FROM lessons WHERE persons_id = " . $_SESSION['PersonID'])) {
        while ($row = $result->fetch_row()) {
            $SelectedItems[$item++] = $row[0];
        }
    }
    return $SelectedItems;
}
