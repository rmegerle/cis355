<?php

include "/home/rtmegerl/php/CRUDPersons.php";
include "/home/rtmegerl/public_html/QuizFunctions.php";
 
// ---------- b. set connection variables and verify connection ---------------
$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection
// ---------- if successful connection...
session_start();

if ($mysqli) {
    $userSelection = 0;
    $firstCall = 1; // first time program is called  
    $deleteLesson = 2; // after user clicked insertSelected button on list 
    $SelectLesson = 3; // after user clicked updateSelected button on list 
    $insertSelected = 4;
    $updateLesson = 5;
    $QuizCompleted = 6;

    $userSelection = $firstCall;
    if (isset($_POST['deleteLesson'])) $userSelection = $deleteLesson;
    if (isset($_POST['SelectLesson'])) $userSelection = $SelectLesson;
    if (isset($_POST['insertSelected'])) $userSelection = $insertSelected;
    if (isset($_POST['updateLesson'])) $userSelection = $updateLesson;
    if (isset($_POST['QuizCompleted'])) $userSelection = $QuizCompleted;

    switch ($userSelection):
        case $deleteLesson:
            echo $_SESSION['test'] = "Hello";
           //deleteTest($mysqli, "quizzes");
//            displayHTMLHead();
//            showQuiz($mysqli, $test);
            break;
        case $SelectLesson :
            echo "Select!!!";
            break;
        case $insertSelected :
            displayHTMLHead();
            showNewQuizForm($mysqli);
            break;
        case $updateLesson :
            echo "Upzzzz Quiz!!!";
            break;
        case $QuizCompleted :
             $CurrentID = CreateQuiz($mysqli);
            displayHTMLHead();
            showQuiz($mysqli, $CurrentID);
            break;
    endswitch;
}
