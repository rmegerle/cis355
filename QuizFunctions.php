<?php
session_start();
function showQuiz($mysqli, $Created = -1) {
    if ($Created == -1) { $LessonIDSelected = $_POST['uid']; } 
    else { $LessonIDSelected = $Created; }
    //HEY, add a form here if you want more buttons on the top
    echo '<div class="col-md-12">
        <form action="LessonGeneration.php">
                <input type="submit" name="updateLesson" value="Back to Lessons" class="btn btn-primary""> </form> <br>
        <form action="QuizOutput.php" method="POST">
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
    $countresult = $mysqli->query("SELECT COUNT(*) FROM quizzes WHERE lessons_id=$LessonIDSelected");
    $countfetch = $countresult->fetch_row();
    $countvalue = $countfetch[0];
    $countresult->close();

// if records > 0 in mysql table, then populate html table, 
// else display "no records" message
    if ($countvalue > 0) {
        populateSelectedQuiz($mysqli, $LessonIDSelected); // populate html table, from mysql table
    } else {
        echo '<p>No records in database table</p><br>';
    }

// display html buttons 
    echo '</table>
        <input type="hidden" id="hid" name="hid" value="">
        <input type="hidden" id="uid" name="uid" value="">
        <input type="submit" name="insertSelected" value="Add an Entry" class="btn btn-primary""></form>';

// below: JavaScript functions at end of html body section
// "hid" is id of item to be deleted
// "uid" is id of item to be updated.
// see also: populateTable function
    echo "<script>
        function setHid(num){
            document.getElementById('hid').value = num;
	}
	function setUid(num) {
            document.getElementById('uid').value = num;
	}
	</script>";
}

function populateSelectedQuiz($mysqli, $LessonIDSelected) {
    if ($result = $mysqli->query("SELECT quizzes.id, lessons.title, lessons.subject, attempts_allowed, "
            . "quizzes.title, quizzes.description FROM lessons LEFT JOIN quizzes ON lessons.id=quizzes.lessons_id "
            . "where quizzes.lessons_id =$LessonIDSelected")) {

        while ($row = $result->fetch_row()) {
            echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' .
            $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] .
            '</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' .
            $row[7] . '</td><td>' . $row[8] . '</td><td>' . $row[9];

            if ($_SESSION["id"] == $row[9]) {
                echo '</td><td><input name="deleteLesson" type="submit" 
                        class="btn btn-danger" value="Delete" onclick="setHid(' . $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit" 
                        name="SelectLesson" class="btn btn-primary" value="Select" 
                        onclick="setUid(' . $row[0] . ');" />';
                echo '<input style="margin-left: 10px;" type="submit" 
                        name="updateLesson" class="btn btn-primary" value="Update" 
                        onclick="setUid(' . $row[0] . ');" />';
            }
        }
    }
    $result->close();
}

function showNewQuizForm($mysqli) {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="QuizOutput.php" 
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

    echo '<tr><td><input type="submit" name="QuizCompleted" class="btn btn-success" value="Add Entry"></td> <td style="text-align: right;">
        <input type="reset" class="btn btn-danger" name="SelectLesson" onclick="history.go(-1);" value="Cancel"></td></tr> </table>
        <a href="LessonGeneration.php" class="btn btn-primary">Display Lessons</a></form></div>';
}

function InsertLessonFieldsCombobox($mysqli) {
    $Output = "";
    $item = 0;
    global $Testing;

    if ($result = $mysqli->query("SELECT id, title, subject FROM lessons")) {
        $Output = '<tr><td>' . "Select Lesson: " . ': </td><td> <select name="lesson_info">';
        while ($row = $result->fetch_row()) {
            $Testing[$item] = "$row[0]" . " - " . "$row[1]" . " - " . "$row[2]";
            $Output .= '<option value="' . $item++ . '"> ' . "$row[0]" . " - " . "$row[1]" . " - " . "$row[2]" . '</option>';
        }
    }
    $Output .= '</select>';
    echo "$Output";
}

function GetSelectedValue($mysqli) {
    $item = 0;
    if ($result = $mysqli->query("SELECT id, title, subject FROM lessons")) {
        while ($row = $result->fetch_row()) {
            $SelectedItems[$item++] = $row[0];
        }
    }
    return $SelectedItems;
}

function CreateQuiz($mysqli) {
    $ItemSelected = GetSelectedValue($mysqli);
    $Item = $_POST['lesson_info'];
    $lesson_id = $ItemSelected[$Item];
    $max_attempts = $_POST['max_attempts'];
    $title = $_POST['quiz_title'];
    $description = $_POST['quiz_des'];

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("INSERT INTO quizzes (id, lessons_id, attempts_allowed, 
				title, description) VALUES ('NULL', '$lesson_id', '$max_attempts', '$title', 
				'$description')")) {
        $stmt->execute();
        $stmt->close();
    }
    return $lesson_id;
}

function deleteTest($mysqli, $Table) {
    $index = $_POST['hid'];  // "hid" is id of db record to be deleted
   $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("DELETE FROM $Table WHERE id='$index'")) {
        // Bind parameters. Types: s=string, i=integer, d=double, etc.
        // protects against sql injections
        //$stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
    return $lesson_id;
}
