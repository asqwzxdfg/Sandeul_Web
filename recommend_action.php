<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid'])) {
    die("로그인이 필요합니다.");
}

$userid = $_SESSION['userid'];
$board_id = $_POST['board_id'];
$action = $_POST['action']; // 'recommend' 또는 'unrecommend'

// 기존 추천 여부 확인
$stmt = $mysqli->prepare("SELECT * FROM board_recommend WHERE board_id = ? AND userid = ?");
$stmt->bind_param("is", $board_id, $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // 이미 했던 추천과 같은 버튼 다시 누르면 취소
    if ($row['action'] === $action) {
        // 추천 취소
        $del_stmt = $mysqli->prepare("DELETE FROM board_recommend WHERE board_id = ? AND userid = ?");
        $del_stmt->bind_param("is", $board_id, $userid);
        $del_stmt->execute();

        // 추천/비추천 수 감소
        if ($action == 'recommend') {
            $mysqli->query("UPDATE board SET recommend_count = recommend_count - 1 WHERE id = $board_id");
        } else {
            $mysqli->query("UPDATE board SET unrecommend_count = unrecommend_count - 1 WHERE id = $board_id");
        }
    } else {
        // 다른 쪽으로 변경
        $update_stmt = $mysqli->prepare("UPDATE board_recommend SET action = ? WHERE board_id = ? AND userid = ?");
        $update_stmt->bind_param("sis", $action, $board_id, $userid);
        $update_stmt->execute();

        if ($action == 'recommend') {
            $mysqli->query("UPDATE board SET recommend_count = recommend_count + 1, unrecommend_count = unrecommend_count - 1 WHERE id = $board_id");
        } else {
            $mysqli->query("UPDATE board SET recommend_count = recommend_count - 1, unrecommend_count = unrecommend_count + 1 WHERE id = $board_id");
        }
    }
} else {
    // 처음 추천/비추천
    $insert_stmt = $mysqli->prepare("INSERT INTO board_recommend (board_id, userid, action) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("iss", $board_id, $userid, $action);
    $insert_stmt->execute();

    if ($action == 'recommend') {
        $mysqli->query("UPDATE board SET recommend_count = recommend_count + 1 WHERE id = $board_id");
    } else {
        $mysqli->query("UPDATE board SET unrecommend_count = recommend_count + 1 WHERE id = $board_id");
    }
}

header("Location: view.php?id=" . $board_id);
?>