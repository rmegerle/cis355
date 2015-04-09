<?php
session_start();
$hostname = "localhost";
$username = "CIS355rtmegerl";
$password = "cis355";
$dbname = "CIS355rtmegerl";
$usertable = "questions";



# ---------- showQuizzes ---------------------------------------------------------
// this function gets records from a "mysql table" and builds an "html table"

function showQuestion($mysqli) {

    //HEY, add a form here if you want more buttons on the top
    echo '<div class="col-md-12">
        <form action="Quizzes.php" method="POST">
                <input type="submit" name="BackToQuizzes" value="Back to Quizzes" class="btn btn-primary""> </form> <br>
        <form action="Questions.php" method="POST">
        <table class="table table-condensed" 
        style="border: 1px solid #dddddd; border-radius: 5px; 
        box-shadow: 2px 2px 10px;">
        <tr><td colspan="11" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;">
        <h2 style="color: white;">Questions</h2>
        </td></tr><tr style="font-weight:800; font-size:20px;">
        <td>ID</td><td>Quiz ID</td><td></td> <td>Question </td></tr>';

// get count of records in mysql table
    $countresult = $mysqli->query("SELECT COUNT(*) FROM questions WHERE quizzes_id= " . $_SESSION['QuizID']);
    $countfetch = $countresult->fetch_row();
    $countvalue = $countfetch[0];
    $countresult->close();

// if records > 0 in mysql table, then populate html table, 
// else display "no records" message
    if ($countvalue > 0) {
        populateQuestions($mysqli); // populate html table, from mysql table
    } else {
        echo '<p>No records in database table</p><br>';
    }

// display html buttons 
    echo '</table>
        <input type="hidden" id="hid" name="hid" value="">
        <input type="hidden" id="uid" name="uid" value="">
        <input type="submit" name="InsertAQuestion" value="Add an Entry" class="btn btn-primary""></form>';

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

function populateQuestions($mysqli) {
    $Quizzes = $_SESSION['QuizID'];

    if ($result = $mysqli->query("SELECT * FROM questions where quizzes_id = " .
            $Quizzes)) {

        while ($row = $result->fetch_row()) {
            echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' .
             '</td><td>' . $row[2] . '</td><td>';

            echo '<input type="submit" name="SelectAQuestion" class="btn btn-primary" value="Select" 
				onclick="setUid(' . $row[0] . ');" />';

            if ($_SESSION['PersonsRole'] == "Teacher" || $_SESSION['PersonsRole'] == "Peer Reviewer" ||
                    $_SESSION['SecRole'] == "Teacher" || $_SESSION['SecRole'] == "Peer Reviewer") {
                echo '</td><td><input name="DeleteAQuestion" type="submit" 
				class="btn btn-danger" value="Delete" onclick="setHid(' .
                $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit" 
				name="UpdateAQuestion" class="btn btn-primary" value="Update" 
				onclick="setUid(' . $row[0] . ');" />';
            }
        }
    }
    $result->close();
}
//
function deleteQuestionRecord($mysqli) {
    $index = $_SESSION['QuestionID'];  // "hid" is id of db record to be deleted
    global $usertable;

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("DELETE FROM $usertable WHERE id='$index'")) {
        $stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
}
//
function ShowQuestionUpdateForm($mysqli) {
    $index = $_SESSION['QuestionID'];  // "uid" is id of db record to be updated 
    if ($result = $mysqli->query("SELECT id, question FROM questions WHERE id = $index")) {
        while ($row = $result->fetch_row()) {
            echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Questions.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Update Question </h2></td></tr>';


            echo '<tr><td>Question: </td><td><textarea maxlength="400" '
            . 'style="resize: none;" name="question_des" cols="51" rows="5">' . 
                    $row[1] . ' </textarea></td></tr>';

            echo '
        </td></tr> 
        <tr><td><input type="submit" name="QuestionExecuteUpdate" class="btn btn-primary" value="Update Entry"></td> 
	</table> <input type="hidden" name="uid" value="' . $row[0] . '"> </form> 
        <form action="Questions.php"> <input type="submit" name="BackToQuestion" value="Back to Question" class="btn btn-primary""> </form> <br> </div>';
        }
        $result->close();
    }
}
//
function showQuestionInsertForm($mysqli) {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Questions.php" 
        onSubmit="return validate();">
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;">
        <h2>Create New Question</h2></td></tr>';


    echo '<tr><td>Quiz Description: </td><td><textarea maxlength="400" style="resize: none;" name="NewQuestion" cols="51" rows="5"></textarea></td></tr>';

    echo '<tr><td><input type="submit" name="QuestionExecuteInsert" class="btn btn-success" value="Add Entry"></td> <td style="text-align: right;"></table>
        <a href="Questions.php" class="btn btn-primary">Display Questions</a></form></div>';
}
//
//function InsertLessonFieldsCombobox($mysqli) {
//    if ($result = $mysqli->query("SELECT id, title, subject FROM lessons WHERE persons_id = " . $_SESSION['PersonID'] . ' AND id = ' . $_SESSION['LessonID'])) {
//        $row = $result->fetch_row();
//        echo '<tr><td>' . "Select Lesson: " . ': </td><td> <input type="edit" name="quiz_title" value= "'
//        . $row[0] . " - " . $row[1] . " - " . $row[2] . '" size="49" readonly></td></tr>';
//    }
//}
//
function CreateQuestion($mysqli) {
    $quiz_id = $_SESSION['QuizID'];
    $description = $_POST['NewQuestion'];

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("INSERT INTO questions (id, quizzes_id, question) VALUES (NULL,'$quiz_id',  '$description')")) {
        $stmt->execute();
        $stmt->close();
    }
    $_SESSION['QuizID'] = $quiz_id;
}
//
function updateQuestion($mysqli) {
    $QuestionUpdated = $_POST['question_des'];
    $QuestionID = $_SESSION['QuestionID'];
    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("UPDATE  questions SET question = '$QuestionUpdated' WHERE id = $QuestionID")) {
        $stmt->execute();
        $stmt->close();
    }
}
//
//function GetSelectedValue($mysqli) {
//    $item = 0;
//    if ($result = $mysqli->query("SELECT id, title, subject FROM lessons WHERE persons_id = " . $_SESSION['PersonID'])) {
//        while ($row = $result->fetch_row()) {
//            $SelectedItems[$item++] = $row[0];
//        }
//    }
//    return $SelectedItems;
//}
