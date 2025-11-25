<?php
session_start();
include "db.php";

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('로그인 후 이용해 주세요');history.back();</script>";
    exit;
}

$post_id = $_POST['post_id'];
$content = $_POST['content'];
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];  // 세션에 저장되어 있어야 함

$sql = "INSERT INTO comments (post_id, userid, username, content) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("isss", $post_id, $userid, $username, $content);
$stmt->execute();

echo "<script>location.href='view.php?id=$post_id';</script>";
?>