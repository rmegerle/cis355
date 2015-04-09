<?php

function showReview($mysqli, $Quiz) {
    if (AlreadyReviewed($mysqli, $Quiz) == 0) { showReviewInsertForm(); } 
    else { PopulateQuizReview($mysqli, $Quiz); }
}

function AlreadyReviewed($mysqli, $QuizID) {
    // get count of records in mysql table
    $countresult = $mysqli->query("SELECT Count(*) FROM `quizReview` WHERE `persons_id` = " . $_SESSION['PersonID']
            . " and `quizzes_id` = " . $QuizID);
    $countfetch = $countresult->fetch_row();
    $countresult->close();
    return $countfetch[0];
}

function showReviewInsertForm() {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="newQuizzesReview.php" 
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

function PopulateQuizReview($mysqli, $Quiz) {
        if ($result = $mysqli->query("SELECT `title`,`rating`,`review` FROM `quizReview` WHERE `quizzes_id` = " . $Quiz)) {
        while ($row = $result->fetch_row()) {
        
        echo '<div class="col-md-4">
        <form name="basic" method="POST" action="newQuizzesReview.php" 
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

