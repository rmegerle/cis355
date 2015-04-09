<?php

session_start();
include "/home/rtmegerl/public_html/CRUDQuizzes.php";
include "/home/rtmegerl/public_html/Functions.php";

$hostname = "localhost";
$username = "CIS355rtmegerl";
$password = "cis355";
$dbname = "CIS355rtmegerl";
$usertable = "quizzes";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection
// ---------- if successful connection...
if ($mysqli) {
    // ---------- c. create table, if necessary -------------------------------
    //createTable($mysqli); 
    // ---------- d. initialize userSelection and $_POST variables ------------
    $userSelection = 0;
    $firstCall = 1; // first time program is called
    $InsertAQuizzes = 2; // after user clicked InsertAQuizzes button on list 
    $UpdateAQuizzes = 3; // after user clicked UpdateAQuizzes button on list 
    $DeleteAQuizzes = 4; // after user clicked DeleteAQuizzes button on list 
    $SelectAQuizzes = 5;
    $QuizzesExecuteInsert = 6; // after user clicked insertSubmit button on form
    $QuizzesExecuteUpdate = 7; // after user clicked updateSubmit button on form
    $BackToLessons = 8;
    $BackToQuizzes = 9;
    $PreviousReview = 10;
    $WriteAReview = 11;
    
    //$_SESSION['QuizID'] = $_POST['uid'];
    $userlocation = $_SESSION['location'];

    $userSelection = $firstCall; // assumes first call unless button was clicked
    if (isset($_POST['InsertAQuizzes'])) { $userSelection = $InsertAQuizzes; }
    if (isset($_POST['UpdateAQuizzes'])) { $userSelection = $UpdateAQuizzes; }
    if (isset($_POST['DeleteAQuizzes'])) { $userSelection = $DeleteAQuizzes; }
    if (isset($_POST['SelectAQuizzes'])) { $userSelection = $SelectAQuizzes; }
    if (isset($_POST['QuizzesExecuteInsert'])) { $userSelection = $QuizzesExecuteInsert; }
    if (isset($_POST['QuizzesExecuteUpdate'])) { $userSelection = $QuizzesExecuteUpdate; }
    if (isset($_POST['BackToLessons']))        { $userSelection = $BackToLessons; }
    if (isset($_POST['BackToQuizzes']))        { $userSelection = $BackToQuizzes; }
    if (isset($_POST['PreviousReview']))       { $userSelection = $PreviousReview; }
    if (isset($_POST['WriteAReview']))         { $userSelection = $WriteAReview; }
    
    switch ($userSelection):
        case $firstCall:
            displayHTMLHead();
            if ($_SESSION['LoggedIn'] == TRUE) {
            showQuizzes($mysqli);
            } else {
                echo "Please Login!";
            }
            break;
        case $InsertAQuizzes:
            displayHTMLHead();
            showQuizzesInsertForm($mysqli);
            break;
        case $UpdateAQuizzes :
            $_SESSION['QuizID'] = $_POST['uid'];
            displayHTMLHead();
            ShowQuizzesUpdateForm($mysqli);
            break;
        case $DeleteAQuizzes:
            $_SESSION['QuizID'] = $_POST['hid'];
            deleteQuizzesRecord($mysqli);   // delete is immediate (no confirmation)
            header("Location: http://csis.svsu.edu/~rtmegerl/Quizzes.php");
            break;
        case $SelectAQuizzes:
            $_SESSION['QuizID'] = $_POST['uid'];
            header("Location: http://csis.svsu.edu/~rtmegerl/Questions.php");
            break;
        case $QuizzesExecuteInsert:
            CreateQuiz($mysqli);
            header("Location: http://csis.svsu.edu/~rtmegerl/Quizzes.php");
            break;
        case $QuizzesExecuteUpdate:
            updateQuizzes($mysqli);
            header("Location: http://csis.svsu.edu/~rtmegerl/Quizzes.php");
            break;
        case $BackToLessons:
            header("Location: http://csis.svsu.edu/~rtmegerl/Lessons.php");
            break;
        case $BackToQuizzes:
            header("Location: http://csis.svsu.edu/~rtmegerl/Quizzes.php");
            break;
        case $PreviousReview:
            $_SESSION['QuizID'] = $_POST['uid'];
            header("Location: http://csis.svsu.edu/~rtmegerl/newQuizzesReview.php");
            break;
        case $WriteAReview:
            $_SESSION['QuizID'] = $_POST['uid'];
            header("Location: http://csis.svsu.edu/~rtmegerl/newQuizzesReview.php");
            break;
    endswitch;
} // ---------- end if ---------- end main processing ----------