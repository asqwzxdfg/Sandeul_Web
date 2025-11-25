<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
    exit;
}

$id = $_POST['id'];
$current_pw = $_POST['current_pw'] ?? '';
$new_pw1 = $_POST['new_pw1'] ?? '';
$new_pw2 = $_POST['new_pw2'] ?? '';

if ($new_pw1 !== $new_pw2) {
    echo "<script>alert('새 비밀번호가 일치하지 않습니다.'); history.back();</script>";
    exit;
}

// 현재 비밀번호 확인
$stmt = $mysqli->prepare("SELECT userpw FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($db_pw_hash);
$stmt->fetch();
$stmt->close();

// 현재 비밀번호 검증
if (!password_verify($current_pw, $db_pw_hash)) {
    echo "<script>alert('현재 비밀번호가 일치하지 않습니다.'); history.back();</script>";
    exit;
}

// 새 비밀번호 암호화
$new_pw_hashed = password_hash($new_pw1, PASSWORD_DEFAULT);

// 비밀번호 업데이트
$stmt = $mysqli->prepare("UPDATE users SET userpw = ? WHERE id = ?");
$stmt->bind_param("si", $new_pw_hashed, $id);
$stmt->execute();
$stmt->close();

echo "<script>alert('비밀번호가 변경되었습니다.'); location.href='mypage.php';</script>";