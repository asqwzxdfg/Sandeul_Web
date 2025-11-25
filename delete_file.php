<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid'])) {
    http_response_code(403);
    echo "unauthorized";
    exit;
}

$file_id = $_POST['file_id'] ?? null;
if (!$file_id) {
    http_response_code(400);
    echo "no file id";
    exit;
}

// 파일 정보 조회
$stmt = $mysqli->prepare("SELECT file_path, board_id FROM board_files WHERE id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$stmt->bind_result($file_path, $board_id);
if ($stmt->fetch()) {
    $stmt->close();

    // 해당 게시글의 작성자인지 확인
    $stmt = $mysqli->prepare("SELECT userid FROM board WHERE id = ?");
    $stmt->bind_param("i", $board_id);
    $stmt->execute();
    $stmt->bind_result($fileOwner);
    $stmt->fetch();
    $stmt->close();

    if ($fileOwner !== $_SESSION['userid']) {
        http_response_code(403);
        echo "unauthorized";
        exit;
    }

    // 실제 파일 삭제
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // DB에서 삭제
    $stmt = $mysqli->prepare("DELETE FROM board_files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $stmt->close();

    echo "success";
} else {
    echo "fail";
}
?>