<?php

session_start();
include "/home/rtmegerl/public_html/CRUDLessons.php";
include "/home/rtmegerl/public_html/Functions.php";

$hostname = "localhost";
$username = "CIS355rtmegerl";
$password = "cis355";
$dbname = "CIS355rtmegerl";
$usertable = "lessons";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection
// ---------- if successful connection...
if ($mysqli) {
    // ---------- c. create table, if necessary -------------------------------
    //createTable($mysqli); 
    // ---------- d. initialize userSelection and $_POST variables ------------
    $userSelection = 0;
    $firstCall = 1; // first time program is called
    $InsertALesson = 2; // after user clicked InsertALesson button on list 
    $UpdateALesson = 3; // after user clicked UpdateALesson button on list 
    $DeleteALesson = 4; // after user clicked DeleteALesson button on list 
    $SelectALesson = 5;
    $LessonExecuteInsert = 6; // after user clicked insertSubmit button on form
    $LessonExecuteUpdate = 7; // after user clicked updateSubmit button on form
    $PreviousReview = 8;
    $WriteAReview = 9;
    
    
    $_SESSION['LessonID'] = $_POST['uid'];
    $userlocation = $_SESSION['location'];

    $userSelection = $firstCall; // assumes first call unless button was clicked
    if (isset($_POST['InsertALesson'])) { $userSelection = $InsertALesson; }
    if (isset($_POST['UpdateALesson'])) { $userSelection = $UpdateALesson; }
    if (isset($_POST['DeleteALesson'])) { $userSelection = $DeleteALesson; }
    if (isset($_POST['SelectALesson'])) { $userSelection = $SelectALesson; }
    if (isset($_POST['LessonExecuteInsert'])) { $userSelection = $LessonExecuteInsert; }
    if (isset($_POST['LessonExecuteUpdate'])) { $userSelection = $LessonExecuteUpdate; }
    if (isset($_POST['PreviousReview'])) { $userSelection = $PreviousReview; }
    if (isset($_POST['WriteAReview'])) { $userSelection = $WriteAReview; }
    
    switch ($userSelection):
        case $firstCall:
            displayHTMLHead();
            if ($_SESSION['LoggedIn'] == TRUE) {
            showLessons($mysqli);
            } else { 
                echo "Please Login!";
                }
            break;
        case $InsertALesson:
            displayHTMLHead();
            showLessonInsertForm($mysqli);
            break;
        case $UpdateALesson :
            $_SESSION['LessonID'] = $_POST['uid'];
            displayHTMLHead();
            ShowLessonsUpdateForm($mysqli);
            break;
        case $DeleteALesson:
            $_SESSION['LessonID'] = $_POST['hid'];
            displayHTMLHead();
            deleteLessonRecord($mysqli);   // delete is immediate (no confirmation)
            header("Location: http://csis.svsu.edu/~rtmegerl/Lessons.php");
            break;
        case $SelectALesson:
            $_SESSION['LessonID'] = $_POST['uid'];
            header("Location: http://csis.svsu.edu/~rtmegerl/Quizzes.php");
            break;
        case $LessonExecuteInsert:
            insertLesson($mysqli);
            header("Location: http://csis.svsu.edu/~rtmegerl/Lessons.php");
            break;
        case $LessonExecuteUpdate:
            updateLesson($mysqli);
            header("Location: http://csis.svsu.edu/~rtmegerl/Lessons.php");
            break;
        case $PreviousReview:
            $_SESSION['LessonID'] = $_POST['uid'];
            header("Location: http://csis.svsu.edu/~rtmegerl/NewLessonReview.php");
            break;
        case $WriteAReview:
            $_SESSION['LessonID'] = $_POST['uid'];
            header("Location: http://csis.svsu.edu/~rtmegerl/NewLessonReview.php");
            break;
    endswitch;
} // ---------- end if ---------- end main processing ----------