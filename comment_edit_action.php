<?php
session_start();
include "db.php";

$id = $_POST['id'];
$post_id = $_POST['post_id'];
$content = $_POST['content'];
$userid = $_SESSION['userid'] ?? null;

// 본인 댓글인지 확인
$sql = "SELECT userid FROM comments WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($owner);
$stmt->fetch();
$stmt->close();

if ($owner !== $userid) {
    echo "<script>alert('수정 권한이 없습니다.');history.back();</script>";
    exit;
}

// 수정 처리
$update = $mysqli->prepare("UPDATE comments SET content = ? WHERE id = ?");
$update->bind_param("si", $content, $id);
$update->execute();

echo "<script>location.href='view.php?id=$post_id';</script>";
?>