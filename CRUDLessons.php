<?php

session_start();
$hostname = "localhost";
$username = "CIS355rtmegerl";
$password = "cis355";
$dbname = "CIS355rtmegerl";
$usertable = "lessons";


# ---------- showLessons ---------------------------------------------------------
// this function gets records from a "mysql table" and builds an "html table"

function showLessons($mysqli) {
    global $usertable;
    echo '<div class="col-md-12">
			<form action="Lessons.php" method="POST">
			<table class="table table-condensed" 
			style="border: 1px solid #dddddd; border-radius: 5px; 
			box-shadow: 2px 2px 10px;">
			<tr><td colspan="11" style="text-align: center; border-radius: 5px; 
			color: white; background-color:#333333;">
			<h2 style="color: white;">Lessons</h2>
			</td></tr><tr style="font-weight:800; font-size:20px;">
			<td>ID</td><td>Title</td><td>Subject</td>
			<td>Description</td><td>Resources</td><td>User</td>
			<td>Date Created</td><td>Search Info</td></tr>';

    // get count of records in mysql table
    $countresult = $mysqli->query("SELECT COUNT(*) FROM $usertable");
    $countfetch = $countresult->fetch_row();
    $countvalue = $countfetch[0];
    $countresult->close();

    // if records > 0 in mysql table, then populate html table, 
    // else display "no records" message
    if ($countvalue > 0) {
        populateLessons($mysqli); // populate html table, from mysql table
    } else {
        echo '<br><p>No records in database table</p><br>';
    }

    // display html buttons 
    echo '</table> ';
        echo '<input type="hidden" id="hid" name="hid" value="">
            <input type="hidden" id="uid" name="uid" value="">
            <input type="submit" name="InsertALesson" value="Add an Entry" class="btn btn-primary"">
            </form></div>';

        echo "<script>
			function setHid(num)
			{
				document.getElementById('hid').value = num;
		    }
		    function setUid(num)
			{
				document.getElementById('uid').value = num;
		    }
		 </script>";
}

function AlreadyReviewed($mysqli, $Lesson) {
    // get count of records in mysql table
    $countresult = $mysqli->query("SELECT Count(*) FROM `lessonReview` WHERE `persons_id` = " . $_SESSION['PersonID']
            . " and `lessons_id` = " . $Lesson);
    $countfetch = $countresult->fetch_row();
    $countresult->close();
    return $countfetch[0];
}

# ---------- populateLessons ----------------------------------------------------
// populate html table, from data in mysql table

function populateLessons($mysqli) {
    global $usertable;
    $Person = $_SESSION['PersonID'];

    if ($result = $mysqli->query("SELECT lessons.id, title, subject, description, resources, "
            . "CONCAT_WS(' ',persons.first_name, persons.last_name) AS person, date_created, "
            . "search_field FROM lessons LEFT JOIN persons ON lessons.persons_id=persons.id WHERE persons_id = $Person")) {
        while ($row = $result->fetch_row()) {
            echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' .
            $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] .
            '</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' .
            $row[7] . '</td><td>';
            
            echo '<input type="submit" input style="margin-left: 10px" name="SelectALesson" class="btn btn-primary" value="Select"  onclick="setUid(' . $row[0] . ');" />';
            if (AlreadyReviewed($mysqli, $row[0]) == 0) {
            echo '<input type="submit" input style="margin-left: 10px" name="WriteAReview" class="btn btn-primary" value="Write Review"  onclick="setUid(' . $row[0] . ');" />';
            } else {
                echo '<input type="submit" input style="margin-left: 10px" name="PreviousReview" class="btn btn-primary" value="Previous Review"  onclick="setUid(' . $row[0] . ');" />';
            }
            if ($_SESSION['PersonsRole'] == "Teacher" || $_SESSION['PersonsRole'] == "Peer Reviewer" ||
                    $_SESSION['SecRole'] == "Teacher" || $_SESSION['SecRole'] == "Peer Reviewer") {
                echo '</td><td><input name="DeleteALesson" type="submit"  class="btn btn-danger" value="Delete" onclick="setHid(' . $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit"  name="UpdateALesson" class="btn btn-primary" value="Update"  onclick="setUid(' . $row[0] . ');" />';
            }
        }
    }
    $result->close();
}

function deleteLessonRecord($mysqli) {
    $index = $_SESSION['LessonID'];  // "hid" is id of db record to be deleted
    global $usertable;
    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("DELETE FROM $usertable WHERE id='$index'")) {
        $stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
}

function ShowLessonsUpdateForm($mysqli) {
    $index = $_POST['uid'];  // "uid" is id of db record to be updated 
    global $usertable;
    if ($result = $mysqli->query("SELECT id, title, subject, description, resources, search_field FROM $usertable WHERE id = $index")) {
        while ($row = $result->fetch_row()) {
            echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Lessons.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Update Lesson</h2></td></tr>';

            echo
            '<tr><td>Title: </td><td><input type="edit" name="title" value="' . $row[1] . '" size="30"></td></tr>
	<tr><td>Subject: </td><td><input type="edit" name="subject" value="' . $row[2] . '" size="30"></td></tr>
	<tr><td>Description: </td><td><input type="edit" name="description" value="' . $row[3] . '" size="20"></td></tr>
	<tr><td>Resources: </td><td><input type="edit" name="resources" value="' . $row[4] . '" size="20"></td></tr>
	<tr><td>Search Keywords: </td><td><input type="edit" name="search_field" value="' . $row[5] . '" size="30"></td></tr>';
            echo '
        </td></tr> 
        <tr><td><input type="submit" name="LessonExecuteUpdate" class="btn btn-primary" value="Update Entry"></td> 
	</table> <input type="hidden" name="uid" value="' . $row[0] . '"> </form> 
        <form action="Lessons.php"> <input type="submit" name="updateLesson" value="Back to Lessons" class="btn btn-primary""> </form> <br> </div>';
        }
        $result->close();
    }
}

function showLessonInsertForm() {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Lessons.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Insert New Lesson</h2></td></tr>';

    echo '<tr><td>Title: </td><td><input type="edit" name="title" value="" 
		size="30"></td></tr>
		<tr><td>Subject: </td><td><input type="edit" name="subject" 
		value="" size="30"></td></tr>
		<tr><td>Description: </td><td><input type="edit" name="description" value="" 
		size="20"></td></tr>
		<tr><td>Resources: </td><td><input type="edit" name="resources" value="" 
		size="20"></td></tr>
		<tr><td>Search Keywords: </td><td><input type="edit" 
                                name="search_field" value="" size="30"></td></tr>';

    echo '<tr><td><input type="submit" name="LessonExecuteInsert" 
        class="btn btn-success" value="Add Entry"></td>
        <td style="text-align: right;"> </table><a href="Lessons.php" 
        class="btn btn-primary">Display Lessons</a></form></div>';
}

function insertLesson($mysqli) {
    global $usertable;

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("INSERT INTO `CIS355rtmegerl`.`lessons` (`id`, `title`, `subject`, "
            . "`description`, `resources`, `persons_id`, `date_created`, `search_field`) VALUES "
            . "(NULL, '" . $_POST['title'] . "', '" . $_POST['subject'] . "', '" . $_POST['description'] . "', '" .
            $_POST['resources'] . "', '" . $_SESSION['PersonID'] . "', '" . date('Y-m-d H:i:s') . "', '" . $_POST['search_field'] . "');")) {

        $stmt->execute();
        $stmt->close();
    }
}

function updateLesson($mysqli) {
    global $usertable;

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("UPDATE  $usertable SET  title =  '" . $_POST['title'] .
            "', subject =  '" . $_POST['subject'] .
            "', description =  '" . $_POST['description'] .
            "', resources =  '" . $_POST['resources'] .
            "', search_field =  '" . $_POST['search_field'] .
            "' WHERE  $usertable .id = " . $_SESSION['LessonID'])) {
        $stmt->execute();
        $stmt->close();
    }
}


