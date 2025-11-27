<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

$id = intval($_GET['id']);
$userid = $_SESSION['userid'];

// 게시글 작성자 확인
$stmt = $mysqli->prepare("SELECT userid FROM board WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($postUserId);
$stmt->fetch();
$stmt->close();

if ($postUserId !== $userid) {
    echo "<script>alert('삭제 권한이 없습니다.'); history.back();</script>";
    exit;
}

// 게시글 삭제
$stmt = $mysqli->prepare("DELETE FROM board WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

echo "<script>alert('게시글이 삭제되었습니다.'); location.href='board.php';</script>";
?>