<?php
include "../connect/connect.php";
include "../connect/session.php";

$boardId = $_GET['boardId'];
$youId = $_SESSION['youId'];

$query = "SELECT boardAuthor FROM sexyBoard WHERE boardId = '{$boardId}'";
$result = $connect->query($query);

if ($result) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if ($row['boardAuthor'] == $youId) {

        $query = "DELETE FROM sexyBoard WHERE boardId = '{$boardId}'";
        $connect->query($query);
    }
}

Header("Location: board.php");
?>