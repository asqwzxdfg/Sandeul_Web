<?php
session_start();
include 'db.php';
if (!isset($_GET['id'])) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}
$id = $_GET['id'];
$board_sql = $mysqli->prepare("SELECT * FROM board WHERE id = ?");
$board_sql->bind_param("i", $id);
$board_sql->execute();
$board_result = $board_sql->get_result();
$board = $board_result->fetch_assoc();

// 로그인한 사용자의 추천 상태 확인
$recommend_status = null;
if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
    $check_sql = $mysqli->prepare("SELECT action FROM board_recommend WHERE board_id = ? AND userid = ?");
    $check_sql->bind_param("is", $id, $userid);
    $check_sql->execute();
    $result = $check_sql->get_result();
    if ($row = $result->fetch_assoc()) {
        $recommend_status = $row['action']; // 'recommend' 또는 'unrecommend'
    }
}

$stmt = $mysqli->prepare("SELECT title, content, userid, username, created_at FROM board WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $content, $userid, $username, $created_at);
$stmt->fetch();
$stmt->close();

$loggedInUserId = $_SESSION['userid'] ?? null;

$cpage = isset($_GET['cpage']) ? (int)$_GET['cpage'] : 1;
$comments_per_page = 10;
$offset = ($cpage - 1) * $comments_per_page;

$count_sql = "SELECT COUNT(*) FROM comments WHERE post_id = ?";
$stmt = $mysqli->prepare($count_sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($total_comments);
$stmt->fetch();
$stmt->close();

$total_pages = ceil($total_comments / $comments_per_page);

$comment_sql = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($comment_sql);
$stmt->bind_param("iii", $id, $comments_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

$file_stmt = $mysqli->prepare("SELECT file_name, file_path FROM board_files WHERE board_id = ?");
$file_stmt->bind_param("i", $id);
$file_stmt->execute();
$file_result = $file_stmt->get_result();
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>게시글 보기 - SANDEUL</title>
    <style>
        body {
            margin: 0;
            padding: 40px;
            background-color: #121212;
            color: white;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
        }
        .title {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .meta {
            font-size: 14px;
            color: #ccc;
            margin-bottom: 30px;
        }
        .content {
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .btn-container {
            margin-top: 40px;
            text-align: right;
            gap: 5px;
        }
        .btn {
            background-color: #1e88e5;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 8px;
            margin-left: 4px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #1565c0;
        }
        .recommend-form {
            margin-top: 20px;
        }

        .recommend-form button {
            padding: 8px 16px;
            margin-right: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            background-color: #333;
            color: #fff;
        }

        .recommend-form .active-recommend {
            background-color: #2e7d32; /* 초록 */
        }

        .recommend-form .active-unrecommend {
            background-color: #c62828; /* 빨강 */
        }

        .recommend-form button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title"><?= htmlspecialchars($title) ?></div>
        <div class="meta">작성자: <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($userid) ?>) | 작성일: <?= htmlspecialchars($created_at) ?></div>
        <?php if ($file_result->num_rows > 0): ?>
        <div style="margin: 15px 0; padding: 5px 10px 0px; background-color: #2a2a2a; border-left: 5px solid #1e88e5; border-radius: 8px;">
        <strong style="color: #90caf9; margin-left: 10px;">  첨부파일</strong>
        <ul style="list-style: none; padding-left: 30px; padding-bottom: 1px; margin-top: 10px;">
            <?php while ($file = $file_result->fetch_assoc()): ?>
                <li style="margin-bottom: 10px;">
                    <a href="<?= htmlspecialchars($file['file_path']) ?>" download style="color: #fff; text-decoration: none;">
                        <?= htmlspecialchars($file['file_name']) ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
        </div>
        <?php endif; ?>
        <div class="content"><?= nl2br(htmlspecialchars($content)) ?></div>

        <div class="btn-container">
        <?php if ($loggedInUserId === $userid): ?>
            <a href="edit.php?id=<?= $id ?>" class="btn">수정</a>
            <a href="delete.php?id=<?= $id ?>" onclick="return confirm('정말 삭제하시겠습니까?');" class="btn">삭제</a>
        <?php endif; ?>
            <a href="board.php" class="btn">목록으로</a>
        </div>
        <!-- 글 내용 아래 추천/비추천 버튼 -->
        <?php if (isset($_SESSION['userid'])): ?>
        <form action="recommend_action.php" method="post" class="recommend-form">
            <input type="hidden" name="board_id" value="<?= $id ?>">
            <button type="submit" name="action" value="recommend" class="<?= $recommend_status == 'recommend' ? 'active-recommend' : '' ?>">
                👍 추천 ( <?= $board['recommend_count'] ?> )
            </button>
            <button type="submit" name="action" value="unrecommend" class="<?= $recommend_status == 'unrecommend' ? 'active-unrecommend' : '' ?>">
                👎 비추천 ( <?= $board['unrecommend_count'] ?> )
            </button>
        </form>
        <?php endif; ?>
<!-- 댓글 영역 시작 -->
<hr style="margin-top: 40px; border-color: #444;">

<div class="comment-box" style="width: 100%; box-sizing: border-box;">
    <h3 style="color: #90caf9;">댓글</h3>

    <!-- 댓글 작성 form -->
    <form action="comment_action.php" method="POST" style="margin-bottom: 20px;">
        <input type="hidden" name="post_id" value="<?= $id ?>">

        <div style="display: flex; flex-direction: column; gap: 10px; font-family: Arial, sans-serif;">
            <textarea name="content" rows="3" placeholder="댓글을 입력하세요" required
                style="width: 100%; box-sizing: border-box; padding: 10px; border-radius: 8px; background-color: #2c2c2c; color: white; border: none; resize: vertical;"></textarea>

            <div style="text-align: right; ">
                <input type="submit" value="댓글 작성"
                    style="background-color: #1e88e5; color: white; padding: 8px 15px; border: none; border-radius: 8px; cursor: pointer;">
            </div>
        </div>
    </form>

    <!-- 댓글 목록 -->
    <?php
    $comment_sql = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $mysqli->prepare($comment_sql);
    $stmt->bind_param("iii", $id, $comments_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='color: #aaa;'>댓글이 없습니다.</p>";
    } else {
        while ($comment = $result->fetch_assoc()) {
            echo "<div style='margin-bottom: 20px; padding: 15px; background-color: #2a2a2a; border-radius: 8px; width: 100%; box-sizing: border-box;'>";
            echo "<div style='color: #64b5f6; font-weight: bold;'>{$comment['username']} <span style='font-size: 12px; color: #aaa;'>({$comment['created_at']})</span></div>";
            echo "<div style='margin-top: 8px; color: #eee;'>".nl2br(htmlspecialchars($comment['content']))."</div>";

            // 🔐 본인일 때만 수정/삭제 버튼 보이기
            if ($loggedInUserId === $comment['userid']) {
                echo "<div style='margin-top: 8px; text-align: right; '>";
                echo "<a href='comment_edit.php?id={$comment['id']}&post_id=$id' style='color: #90caf9; margin-right: 10px; text-decoration: none; font-size: 12px;'>수정</a>";
                echo "<a href='comment_delete.php?id={$comment['id']}&post_id=$id' onclick='return confirm(\"정말 삭제하시겠습니까?\");' style='color: #ef5350; text-decoration: none; font-size: 12px;'>삭제</a>";
                echo "</div>";
            }
            echo "</div>";
        }
    }
    ?>
    <!-- 댓글 페이징 -->
    <?php if ($total_pages > 1): ?>
    <div style="text-align: center; margin-top: 20px;">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="view.php?id=<?= $id ?>&cpage=<?= $i ?>" style="margin: 0 5px; color: <?= ($i == $cpage ? '#1e88e5' : '#ccc') ?>; text-decoration: none;">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

</body>
</html>