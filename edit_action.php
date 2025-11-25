<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$userid = $_SESSION['userid'];

if (!$id || !$title || !$content) {
    echo "<script>alert('모든 항목을 입력해주세요.'); history.back();</script>";
    exit;
}

// 게시글 작성자 확인
$stmt = $mysqli->prepare("SELECT userid FROM board WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($postUserId);
$stmt->fetch();
$stmt->close();

if ($postUserId !== $userid) {
    echo "<script>alert('수정 권한이 없습니다.'); history.back();</script>";
    exit;
}

// 수정 실행
$stmt = $mysqli->prepare("UPDATE board SET title = ?, content = ? WHERE id = ?");
$stmt->bind_param("ssi", $title, $content, $id);

if ($stmt->execute()) {
    echo "<script>alert('게시글이 수정되었습니다.'); location.href='view.php?id=$id';</script>";
} else {
    echo "<script>alert('수정 중 오류가 발생했습니다.'); history.back();</script>";
}

$stmt->close();

if (isset($_POST['delete_files'])) {
    foreach ($_POST['delete_files'] as $file_id) {
        // 파일 경로 조회
        $stmt = $mysqli->prepare("SELECT file_path FROM board_files WHERE id = ? AND board_id = ?");
        $stmt->bind_param("ii", $file_id, $id);
        $stmt->execute();
        $stmt->bind_result($file_path);
        if ($stmt->fetch()) {
            if (file_exists($file_path)) {
                unlink($file_path); // 실제 파일 삭제
            }
        }
        $stmt->close();

        // DB에서 파일 정보 삭제
        $stmt = $mysqli->prepare("DELETE FROM board_files WHERE id = ? AND board_id = ?");
        $stmt->bind_param("ii", $file_id, $id);
        $stmt->execute();
    }
}

if (isset($_FILES['upload_files'])) {
    foreach ($_FILES['upload_files']['name'] as $key => $name) {
        if ($_FILES['upload_files']['error'][$key] === 0) {
            $tmp_name = $_FILES['upload_files']['tmp_name'][$key];
            $safe_name = basename($name);
            $stored_name = time() . "_" . $safe_name;
            $file_path = "uploads/" . $stored_name;

            move_uploaded_file($tmp_name, $file_path);

            $stmt = $mysqli->prepare("INSERT INTO board_files (board_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id, $safe_name, $file_path);
            $stmt->execute();
        }
    }
}

?>