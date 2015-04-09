<?php
session_start();
function showLessons($mysqli) {
    global $usertable;
    $usertable = 'lessons';
    echo '<div class="col-md-12">
			<form action="LessonGeneration.php" method="POST">
			<table class="table table-condensed" 
			style="border: 1px solid #dddddd; border-radius: 5px; 
			box-shadow: 2px 2px 10px;">
			<tr><td colspan="11" style="text-align: center; border-radius: 5px; 
			color: white; background-color:#333333;">
			<h2 style="color: white;">Lessons</h2>
			</td></tr><tr style="font-weight:800; font-size:20px;">
			<td>ID</td><td>Title</td><td>Subject</td>
			<td>Description</td><td>Resources</td><td>Created By</td>
			<td>Date Created</td><td>Search Keywords</td></tr>';

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
    echo '</table><input type="hidden" id="hid" name="hid" value="">
        <input type="hidden" id="uid" name="lesson_id" value="">
        <input type="submit" name="insertSelected" value="Add an Entry" 
        class="btn btn-primary""> </form></div>';

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
        echo "<script>
        function setHid(num){
            document.getElementById('hid').value = num;
	}
	function setLesson(num) {
            document.getElementById('lesson_id').value = num;
	}
	</script>";
}

// populate html table, from data in mysql table
function populateLessons($mysqli) {
    global $usertable;
    $usertable = 'lessons';

    if ($result = $mysqli->query("SELECT lessons.id, title, subject, description, resources, "
            . "CONCAT_WS(' ',persons.first_name, persons.last_name) AS person, date_created, "
            . "search_field FROM lessons LEFT JOIN persons ON lessons.persons_id=persons.id")) {

        while ($row = $result->fetch_row()) {
            echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' .
            $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] .
            '</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' .
            $row[7] . '</td><td>' . $row[8] . '</td><td>' . $row[9];

            if ($_SESSION["id"] == $row[9]) {
                echo '<input type="hidden" id="uid" name="uid" value="' . $row[0] . '">
                    </td><td><input name="deleteLesson" type="submit" 
                        class="btn btn-danger" value="Delete" onclick="setHid(' . $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit" 
                        name="SelectLesson" class="btn btn-primary" value="Select" 
                        onclick="setLesson(' . $row[0] . ');" />';
                echo '<input style="margin-left: 10px;" type="submit" 
                        name="updateLesson" class="btn btn-primary" value="Update" 
                        onclick="setUid(' . $row[0] . ');" />';
            }
        }
    }
    $result->close();
}

function showUpdateForm($mysqli, $Table) {
    $index = $_POST['uid'];  // "uid" is id of db record to be updated 

    if ($result = $mysqli->query("SELECT id, title, subject, description, resources, search_field FROM $Table WHERE id = $index")) {
        while ($row = $result->fetch_row()) {
            echo '<div class="col-md-4">
        <form name="basic" method="POST" action="LessonGeneration.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Insert New Lesson</h2></td></tr>';

            echo 
        '<tr><td>Title: </td><td><input type="edit" name="title" value="' . $row[1] . '" size="30"></td></tr>
	<tr><td>Subject: </td><td><input type="edit" name="subject" value="' . $row[2] . '" size="30"></td></tr>
	<tr><td>Description: </td><td><input type="edit" name="description" value="' . $row[3] . '" size="20"></td></tr>
	<tr><td>Resources: </td><td><input type="edit" name="resources" value="' . $row[4] . '" size="20"></td></tr>
	<tr><td>Search Keywords: </td><td><input type="edit" name="search_field" value="' . $row[5] . '" size="30"></td></tr>';
            echo '
        </td></tr> 
        <tr><td><input type="submit" name="updateLessonDone" class="btn btn-primary" value="Update Entry"></td> 
	</table> <input type="hidden" name="uid" value="' . $row[0] . '"> </form> 
        <form action="LessonGeneration.php"> <input type="submit" name="updateLesson" value="Back to Lessons" class="btn btn-primary""> </form> <br> </div>';
        }
        $result->close();
    }
}


/* ------------------------------------------------------------------------------------------------------------------- */

//ADD ENTRY IS NOT DONE YET!
function showLessonInsertForm() {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="LessonGeneration.php" 
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

    echo '<tr><td><input type="submit" name="insertCompleted" 
        class="btn btn-success" value="Add Entry"></td>
        <td style="text-align: right;"> </table><a href="LessonGeneration.php" 
        class="btn btn-primary">Display Database</a></form></div>';
}

/* ------------------------------------------------------------------------------------------------------------------- */

function updateRecord($mysqli, $Table) {
    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("UPDATE  $Table SET  title =  '" . $_POST['title'] .
                                 "', subject =  '" . $_POST['subject'] .
                                 "', description =  '" . $_POST['description'] .
                                 "', resources =  '" . $_POST['resources'] .
                                 "', search_field =  '" . $_POST['search_field'] .
                                 "' WHERE  $Table .id = " . $_POST['uid'])) {
        $stmt->execute();
        $stmt->close();
    }
}
