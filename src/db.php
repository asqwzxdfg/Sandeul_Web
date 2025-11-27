<?php
// 환경변수 있으면 그걸 쓰고, 없으면 기존 값 사용
$host = getenv('DB_HOST') ?: "localhost";
$adminid = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "1201";
$databaseName = getenv('DB_NAME') ?: "member_db";

$mysqli = new mysqli($host, $adminid, $password, $databaseName);
if ($mysqli->connect_errno) {
    die("DB 연결 실패: ". $mysqli->connect_error);
}
?>