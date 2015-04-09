<?php

session_start();
include "/home/rtmegerl/public_html/CRUDQuestions.php";
include "/home/rtmegerl/public_html/Functions.php";

$hostname = "localhost";
$username = "CIS355rtmegerl";
$password = "cis355";
$dbname = "CIS355rtmegerl";
$usertable = "questions";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection
// ---------- if successful connection...
if ($mysqli) {
    // ---------- c. create table, if necessary -------------------------------
    //createTable($mysqli); 
    // ---------- d. initialize userSelection and $_POST variables ------------
    $userSelection = 0;
    $firstCall = 1; // first time program is called
    $InsertAQuestion = 2; // after user clicked InsertAQuestion button on list 
    $UpdateAQuestion = 3; // after user clicked UpdateAQuizzes button on list 
    $DeleteAQuestion = 4; // after user clicked DeleteAQuizzes button on list 
    $SelectAQuestion = 5;
    $QuestionExecuteInsert = 6; // after user clicked insertSubmit button on form
    $QuestionExecuteUpdate = 7; // after user clicked updateSubmit button on form
    $BackToLessons = 8;
    $BackToQuestion = 9;
    
    //$_SESSION['QuizID'] = $_POST['uid'];
    $userlocation = $_SESSION['location'];

    $userSelection = $firstCall; // assumes first call unless button was clicked
    if (isset($_POST['InsertAQuestion'])) { $userSelection = $InsertAQuestion; }
    if (isset($_POST['UpdateAQuestion'])) { $userSelection = $UpdateAQuestion; }
    if (isset($_POST['DeleteAQuestion'])) { $userSelection = $DeleteAQuestion; }
    if (isset($_POST['SelectAQuestion'])) { $userSelection = $SelectAQuestion; }
    if (isset($_POST['QuestionExecuteInsert'])) { $userSelection = $QuestionExecuteInsert; }
    if (isset($_POST['QuestionExecuteUpdate'])) { $userSelection = $QuestionExecuteUpdate; }
    if (isset($_POST['BackToQuizzes'])) { $userSelection = $BackToLessons; }
    if (isset($_POST['BackToQuestion'])) { $userSelection = $BackToQuestion; }

    switch ($userSelection):
        case $firstCall:
            displayHTMLHead();
            if ($_SESSION['LoggedIn'] == TRUE) {
            showQuestion($mysqli);
            } else {
                echo "Please Login!";
            }
            break;
        case $InsertAQuestion:
            displayHTMLHead();
            showQuestionInsertForm($mysqli);
            break;
        case $UpdateAQuestion :
            $_SESSION['QuestionID'] = $_POST['uid'];
            displayHTMLHead();
            ShowQuestionUpdateForm($mysqli);
            break;
        case $DeleteAQuestion:
            $_SESSION['QuestionID'] = $_POST['hid'];
            deleteQuestionRecord($mysqli);   // delete is immediate (no confirmation)
            header("Location: http://csis.svsu.edu/~rtmegerl/Questions.php");
            break;
        case $SelectAQuestion:
            $_SESSION['QuestionID'] = $_POST['uid'];
            echo "I think this will show the questions for each question to take but this"
            . "will like just like the Quizzes linked to this form!";
            break;
        case $QuestionExecuteInsert:
            CreateQuestion($mysqli);
            header("Location: http://csis.svsu.edu/~rtmegerl/Questions.php");
            break;
        case $QuestionExecuteUpdate:
            updateQuestion($mysqli);
            header("Location: http://csis.svsu.edu/~rtmegerl/Questions.php");
            break;
        case $BackToQuestion:
            header("Location: http://csis.svsu.edu/~rtmegerl/Questions.php");
            break;
    endswitch;
} // ---------- end if ---------- end main processing ----------