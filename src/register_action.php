<?php
include 'db.php';

$userid = $_POST['userid'];
$userpw = password_hash($_POST['userpw'], PASSWORD_DEFAULT);
$username = $_POST['username'];

$stmt = $mysqli->prepare("INSERT INTO users (userid, userpw, username) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $userid, $userpw, $username);
if ($stmt->execute()) {
    echo "<script>alert('회원가입 완료!'); location.href='index.php'; </script>";
}   else {
    echo "<script>alert('회원가입 실패.'); history.back();</script>";
}
?>