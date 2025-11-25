<?php
session_start();
include "db.php";

$comment_id = $_GET['id'] ?? null;
$post_id = $_GET['post_id'] ?? null;
$userid = $_SESSION['userid'] ?? null;

$sql = "SELECT userid FROM comments WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->bind_result($owner);
$stmt->fetch();
$stmt->close();

if ($owner !== $userid) {
    echo "<script>alert('삭제 권한이 없습니다.');history.back();</script>";
    exit;
}

// 삭제 처리
$delete = $mysqli->prepare("DELETE FROM comments WHERE id = ?");
$delete->bind_param("i", $comment_id);
$delete->execute();

echo "<script>location.href='view.php?id=$post_id';</script>";
?>