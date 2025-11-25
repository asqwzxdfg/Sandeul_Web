<?php
session_start();
include 'db.php';

$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
$title = $_POST['title'];
$content = $_POST['content'];

$file_name = null;
$file_path = null;

$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 게시글 등록
$stmt = $mysqli->prepare("INSERT INTO board (title, content, userid, username, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("ssss", $title, $content, $userid, $username);
$stmt->execute();
$board_id = $stmt->insert_id;

// 파일 업로드 처리
foreach ($_FILES['upload_files']['name'] as $key => $name) {
    if ($_FILES['upload_files']['error'][$key] === 0) {
        $tmp_name = $_FILES['upload_files']['tmp_name'][$key];
        $safe_name = basename($name);
        $stored_name = time() . "_" . $safe_name;
        $file_path = $upload_dir . $stored_name;

        move_uploaded_file($tmp_name, $file_path);

        // DB에 파일 정보 저장
        $stmt = $mysqli->prepare("INSERT INTO board_files (board_id, file_name, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $board_id, $safe_name, $file_path);
        $stmt->execute();
    }
}

if ($stmt->affected_rows > 0) {
    echo "<script>alert('글이 등록되었습니다.'); location.href='board.php';</script>";
} else {
    echo "<script>alert('오류가 발생했습니다.'); history.back();</script>";
}