<?php
session_start();
include "/home/rtmegerl/public_html/CRUDPersons.php";
include "/home/rtmegerl/public_html/LessonFunctions.php";
include "/home/rtmegerl/public_html/QuizFunctions.php";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli);

if ($mysqli) {
    $userSelection = 0;
    $firstCall = 1;
    $deleteLesson = 2;
    $SelectLesson = 3;
    $insertSelected = 4;
    $updateLesson = 5;
    $updateLessonDone = 6;
    
    $userSelection = $firstCall;
    if (isset($_POST['deleteLesson']))
        $userSelection = $deleteLesson;
    if (isset($_POST['SelectLesson']))
        $userSelection = $SelectLesson;
    if (isset($_POST['insertSelected']))
        $userSelection = $insertSelected;
    if (isset($_POST['updateLesson']))
        $userSelection = $updateLesson;
    if (isset($_POST['updateLessonDone']))
        $userSelection = $updateLessonDone;
    switch ($userSelection):
        case $firstCall:
            displayHTMLHead();
            showLessons($mysqli);
            break;
        case $deleteLesson:
            deleteRecord($mysqli, "lessons");
            displayHTMLHead();
            showLessons($mysqli);
            break;
        case $SelectLesson :
            displayHTMLHead();
            showQuiz($mysqli);
            break;
        case $insertSelected :
            displayHTMLHead();
            showLessonInsertForm();
            break;
        case $updateLesson :
            displayHTMLHead();
            showUpdateForm($mysqli, "lessons");
            break;
        case $updateLessonDone:
            updateRecord($mysqli, "lessons");
            displayHTMLHead();
            showLessons($mysqli);
            break;
    endswitch;
}

