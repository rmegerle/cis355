<?php

session_start();
function showReviewInsertForm() {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="NewLessonReview.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Add a Review</h2></td></tr>';

    echo '<tr><td>Title: </td><td><input type="edit" name="Title" value="" size="30"></td></tr>
          <tr><td>Rating: </td><td><input type="number" name="Rating" min="1" max="5"></td></tr>
	  <tr><td>Comment: </td><td><textarea maxlength="2000" style="resize: none;" name="ReviewComment" cols="75" rows="10"></textarea></td></tr>';

echo '<tr><td><input type="submit" name="Back" class="btn btn-primary" value="Go Back"  onclick="javascript:history.back();"/></td>';
echo '<td><input type="submit" input style="margin-left: 10px" name="EnteringReview" class="btn btn-primary pull-right" value="Submit"/></td></tr></form></div>';
}

function insertLessonReview($mysqli) {
    $Title = $_POST['Title'];
    $Rating = $_POST['Rating'];
    $ReviewComment = $_POST['ReviewComment'];
    
    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("INSERT INTO `lessonReview` (`id`, `persons_id`, `lessons_id`, `title`, "
            . "`review`, `date_submitted`, `rating`) VALUES (NULL, '" . $_SESSION['PersonID'] . "', '" . $_SESSION['LessonID'] . "', '" 
            . $Title . "', '" . $ReviewComment . "', '" . date('Y-m-d H:i:s') . "', '" . $Rating . "');")) {

        $stmt->execute();
        $stmt->close();
    }
}

function AlreadyReviewed($mysqli, $Lesson) {
    // get count of records in mysql table
    $countresult = $mysqli->query("SELECT Count(*) FROM `lessonReview` WHERE `persons_id` = " . $_SESSION['PersonID']
            . " and `lessons_id` = " . $Lesson);
    $countfetch = $countresult->fetch_row();
    $countresult->close();
    return $countfetch[0];
}

function PopulateLessonReview($mysqli, $Lessson) {
        if ($result = $mysqli->query("SELECT `title`,`rating`,`review` FROM `lessonReview` WHERE `lessons_id` = " . $Lessson)) {
        while ($row = $result->fetch_row()) {
        
        echo '<div class="col-md-4">
        <form name="basic" method="POST" action="NewLessonReview.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Add a Review</h2></td></tr>';

        echo '<tr><td>Title: </td><td><input type="edit" name="Title" value="' . $row[0] . '" size="30"></td></tr>
          <tr><td>Rating: </td><td><input type="number" name="Rating" value="' . $row[1] . '" min="1" max="5"></td></tr>
	  <tr><td>Comment: </td><td><textarea maxlength="2000" style="resize: none;" name="ReviewComment" cols="75" rows="10">' . $row[2] . '</textarea></td></tr>';

        echo '<tr><td><input type="submit" name="Back" class="btn btn-primary" value="Go Back"  onclick="javascript:history.back();"/></td>';
        echo '<td><input type="submit" input style="margin-left: 199px" name="DeleteReview" class="btn btn-danger" value="Delete"/>';
        echo '<input type="submit" input style="margin-left: 210px" name="UpdatingReview" class="btn btn-primary" value="Update"/></td></tr></form></div>';
      }
   }
}

function showReview($mysqli, $Lessson) {
    if (AlreadyReviewed($mysqli, $Lessson) == 0) { showReviewInsertForm(); } 
    else { PopulateLessonReview($mysqli, $Lessson); }
}

function updateLessonReview($mysqli) {
    $Title = $_POST['Title'];
    $Rating = $_POST['Rating'];
    $ReviewComment = $_POST['ReviewComment'];
    
    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("UPDATE  `lessonReview` SET  `title` =  '$Title', `rating` =  '$Rating', `review` = '$ReviewComment' WHERE  `lessons_id` = " . $_SESSION['LessonID'])) {

        $stmt->execute();
        $stmt->close();
    }
}

function DeleteLessonReview($mysqli) {
    $Title = $_POST['Title'];
    $Rating = $_POST['Rating'];
    $ReviewComment = $_POST['ReviewComment'];
    
    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("DELETE FROM `lessonReview` WHERE `lessons_id` = " . $_SESSION['LessonID'])) {

        $stmt->execute();
        $stmt->close();
    }
}