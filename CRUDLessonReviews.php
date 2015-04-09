<?php

session_start();
$hostname = "localhost";
$username = "CIS355rtmegerl";
$password = "cis355";
$dbname = "CIS355rtmegerl";
$usertable = "lessons";


# ---------- showLessons ---------------------------------------------------------
// this function gets records from a "mysql table" and builds an "html table"

function showReviews($mysqli) {
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
    $countresult = $mysqli->query("SELECT COUNT(*) FROM `lessonReview`");
    $countfetch = $countresult->fetch_row();
    $countvalue = $countfetch[0];
    $countresult->close();

    // if records > 0 in mysql table, then populate html table, 
    // else display "no records" message
    if ($countvalue > 0) {
        populateReviews($mysqli); // populate html table, from mysql table
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

function populateReviews($mysqli) {
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
            echo '<input type="submit" input style="margin-left: 10px" name="PreviousReview" class="btn btn-primary" value="Previous Review"  onclick="setUid(' . $row[0] . ');" />';
            echo '<input type="submit" input style="margin-left: 10px" name="WriteAReview" class="btn btn-primary" value="Write Review"  onclick="setUid(' . $row[0] . ');" />';
            if ($_SESSION['PersonsRole'] == "Teacher" || $_SESSION['PersonsRole'] == "Peer Reviewer" ||
                    $_SESSION['SecRole'] == "Teacher" || $_SESSION['SecRole'] == "Peer Reviewer") {
                echo '</td><td><input name="DeleteALesson" type="submit"  class="btn btn-danger" value="Delete" onclick="setHid(' . $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit"  name="UpdateALesson" class="btn btn-primary" value="Update"  onclick="setUid(' . $row[0] . ');" />';
            }
        }
    }
    $result->close();
}