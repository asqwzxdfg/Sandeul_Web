<?php
$host = "localhost";
$adminid = "root";
$password = "1201";
$databaseName = "member_db";
$mysqli = new mysqli($host, $adminid, $password, $databaseName);
if ($mysqli->connect_errno) {
    die("DB 연결 실패: ". $mysqli->connect_error);
}
?>