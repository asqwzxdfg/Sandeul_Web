<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
    exit;
}

$id = $_POST['id'] ?? '';
$username = trim($_POST['username'] ?? '');

if ($username === '') {
    echo "<script>alert('이름을 입력해주세요.'); history.back();</script>";
    exit;
}

$stmt = $mysqli->prepare("UPDATE users SET username = ? WHERE id = ?");
$stmt->bind_param("si", $username, $id);
$stmt->execute();
$stmt->close();

echo "<script>alert('정보가 수정되었습니다.'); location.href='mypage.php';</script>";