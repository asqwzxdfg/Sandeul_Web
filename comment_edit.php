<?php
session_start();
include "db.php";

if (!isset($_GET['id']) || !isset($_GET['post_id'])) {
    echo "<script>alert('잘못된 접근입니다.');history.back();</script>";
    exit;
}

$comment_id = $_GET['id'];
$post_id = $_GET['post_id'];
$userid = $_SESSION['userid'] ?? null;

$sql = "SELECT * FROM comments WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

if (!$comment || $comment['userid'] !== $userid) {
    echo "<script>alert('권한이 없습니다.');history.back();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>댓글 수정</title>
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 290px auto;
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
        }
        h2 {
            margin-bottom: 15px;
            color: #90caf9;
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 12px;
            font-size: 16px;
            background-color: #2c2c2c;
            color: white;
            border: 1px solid #444;
            border-radius: 8px;
            resize: none;
            font-family:  'Segoe UI', sans-serif;
            box-sizing: border-box
        }
        button[type="submit"] {
            background-color: #1e88e5;
            border: none;
            padding: 8px 14px;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #1565c0;
        }
        .back-link {
            margin-left: 3px;
            color: #90caf9;
            text-decoration: none;
            font-size: 15px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
        .form-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>댓글 수정</h2>
        <form action="comment_edit_action.php" method="POST">
            <input type="hidden" name="id" value="<?= $comment_id ?>">
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <textarea name="content" required><?= htmlspecialchars($comment['content']) ?></textarea>
            <div class="form-buttons"> 
                <a href="view.php?id=<?= $post_id ?>" class="back-link">뒤로가기</a>
                <button type="submit">수정 완료</button>
            </div>
        </form>
        
    </div>
</body>
</html>