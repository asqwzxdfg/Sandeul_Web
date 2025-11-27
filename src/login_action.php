<?php
session_start();
include 'db.php';

$userid = $_POST['userid'];
$userpw = $_POST['userpw'];

$stmt = $mysqli->prepare("SELECT userpw, username FROM users WHERE userid = ?");
$stmt->bind_param("s", $userid);
$stmt->execute();
$stmt->bind_result($hased_pw, $username);
$stmt->fetch();

if (password_verify($userpw, $hased_pw)) {
    $_SESSION['userid'] = $userid;
    $_SESSION['username'] = $username;
    echo "<script>alert('{$username}님 환영합니다!'); location.href='index.php';</script>";
} else {
    echo "<script>alert('아이디 또는 비밀번호가 틀렸습니다.'); history.back();</script>";
}
?>