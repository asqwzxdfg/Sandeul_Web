<?php
include 'db.php';

$userid = $_POST['userid'] ?? '';
$username = $_POST['username'] ?? '';

if (!$userid || !$username) {
    echo "<script>alert('모든 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

// 해당 유저 확인
$stmt = $mysqli->prepare("SELECT id FROM users WHERE userid = ? AND username = ?");
$stmt->bind_param("ss", $userid, $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // 임시 비밀번호 생성
    $temp_pw = substr(bin2hex(random_bytes(4)), 0, 8); // 예: 8자리
    $hashed_pw = password_hash($temp_pw, PASSWORD_DEFAULT);

    // 비밀번호 업데이트
    $update = $mysqli->prepare("UPDATE users SET userpw = ? WHERE userid = ?");
    $update->bind_param("ss", $hashed_pw, $userid);
    $update->execute();

    echo "<script>alert('임시 비밀번호는: $temp_pw 입니다. 로그인 후 비밀번호를 변경해주세요.'); location.href='login.php';</script>";
} else {
    echo "<script>alert('일치하는 회원이 없습니다.'); history.back();</script>";
}
?>