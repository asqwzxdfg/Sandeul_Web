<?php
session_start();
include 'db.php';

//페이징 관련 설정
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;

$searchType = $_GET['type'] ?? '';
$searchKeyword = $_GET['keyword'] ?? '';

//검색 조건 적용
$whereClause = '';
if ($searchType && $searchKeyword) {
    $safeKeyword = $mysqli->real_escape_string($searchKeyword);
    if ($searchType === 'title') {
        $whereClause = "WHERE title LIKE '%$safeKeyword%'";
    } elseif ($searchType === 'username') {
        $whereClause = "WHERE username LIKE '%$safeKeyword%'";
    }
}

// 총 게시글 수 가져오기
$countResult = $mysqli->query("SELECT COUNT(*) AS total FROM board $whereClause");
$totalRow = $countResult->fetch_assoc();
$totalPosts = $totalRow['total'];

$totalPages = ceil($totalPosts / $perPage);

//페이지 표시할 글 불러오기
$result = $mysqli->query("SELECT * FROM board $whereClause ORDER BY id DESC LIMIT $start, $perPage");
$loggedIn = isset($_SESSION['userid']);
$username = $_SESSION['username'] ?? '';

//추천 수 가져오기
$result = $mysqli->query("
    SELECT 
        b.*, 
        (SELECT COUNT(*) FROM board_recommend r WHERE r.board_id = b.id AND r.action = 'recommend') AS like_count,
        (SELECT COUNT(*) FROM board_recommend r WHERE r.board_id = b.id AND r.action = 'unrecommend') AS dislike_count
    FROM board b
    $whereClause
    ORDER BY b.id DESC
    LIMIT $start, $perPage
");

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판 | SANDEUL</title>
    <style>
        body{
            margin: 0;
            background-color: #121212;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }
        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        .top-bar {
            width: 60%;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .btn {
            padding: 8px 15px;
            font-size: 16px;
            background-color: #1e88e5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #1565c0;
        }
        table {
            width: 60%;
            border-collapse: collapse;
            background-color: #1e1e1e;
        }
        th, td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #333;
        }
        th {
            background-color: #2a2a2a;
        }
        tr.data-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        tr.data-row:hover {
            background-color: #2a2a2a;
        }
        th.no, td.no {
            width: 8%;
        }
        th.title, td.title {
            width: 54%;
        }
        th.writer, td.writer{
            width: 15%;
        }
        th.date, td.date {
            width: 18%
        }
        th.rec, td.rec {
            width: 5$
        }

        .pagination {
            display: flex;
            font-size: 18px;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            align-items: center;
        }
        .pagination span,
        .pagination a {
            color: white;
            text-decoration: none;
            font-weight: normal;
        }
        .pagination a:hover {
            text-decoration: underline;
        }
        .pagination .current {
            font-weight: bold;
            text-decoration: underline;
        }
        .pagination .disabled {
            opacity: gray;
            cursor: default;
            pointer-events: none;
        }
        .page-btn {
            padding: 0px 2px;
            background-color: #1e1e1e;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 16px;
        }
        .page-btn:hover {
            background-color: #2e2e2e;
        }
        .page-btn.disabled {
            background-color: #1e1e1e;
            color: gray;
            cursor: default;
            pointer-events: none;
        }
        .search-form {
            width: 60%;
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .search-form select,
        .search-form input[type="text"] {
            padding: 3px;
            font-size: 14px;
            border-radius: 4px;
            border: none;
        }
        .search-form button {
            padding: 3px 12px;
            font-size: 14px;
            background-color: #1e88e5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #1565c0;
        }
        .no-result {
            texe-align: center;
            padding: 10px;
            font-size: 16px;
            color: #ccc;
        }
    </style>
</head>
<body>
    <h1>게시판</h1>
    <div class="welcome"><?= htmlspecialchars($username) ?>님 환영합니다.</div>
    <div class="welcome">개발중입니다.</div>
    <div class="top-bar">
        <a href="index.php" class="btn">메인으로</a>
        <a href="write.php" class="btn">글쓰기</a>
    </div>

    <table>
        <thead>
        <tr>
            <th class="no">번호</th>
            <th class="title">제목</th>
            <th class="writer">작성자</th>
            <th class="date">작성일</th>
            <th class="rec">추천</th>
        </tr>
        </thead>
        <tbody>
        <?php 
        if ($result->num_rows > 0):
            $displayNumber = $totalPosts - $start;
            while ($row = $result->fetch_assoc()): ?>
            <tr class="data-row" onclick="location.href='view.php?id=<?= $row['id'] ?>'">
                <td class="no"><?= $displayNumber-- ?></td>
                <td class="title"><?= htmlspecialchars($row['title']) ?></td>
                <td class="writer"><?= htmlspecialchars($row['username']) ?></td>
                <td class="date"><?= $row['created_at'] ?></td>
                <td class="rec"><?= $row['like_count'] ?></td>
            </tr>
            <?php endwhile;
            else: ?>
                <tr><td colspan="4" class="no-result">검색 결과가 없습니다.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- 검색 폼 -->
    <form method="get" class="search-form">
        <select name="type">
            <option value="title" <?= $searchType === 'title' ? 'selected' : '' ?>>제목</option>
            <option value="username" <?= $searchType === 'username' ? 'selected' : '' ?>>작성자</option>
        </select>
        <input type="text" name="keyword" value="<?= htmlspecialchars($searchKeyword) ?>" placeholder = "검색어 입력">
        <button type="submit">검색</button>
    </form>

    <!-- 페이지네이션 -->
    <div class="pagination">
        <?php
        $maxPageLinks = 5;
        $half = floor($maxPageLinks / 2);

        $startPage = max(1, $page - $half);
        $endPage = min($totalPages, $startPage + $maxPageLinks - 1);

        if ($endPage - $startPage < $maxPageLinks - 1) {
            $startPage = max(1, $endPage - $maxPageLinks + 1);
        }

        $queryStr = "&type=$searchType&keyword=" . urlencode($searchKeyword);

        if ($page > 1): ?>
            <a href="?page=1<?= $queryStr ?>"><button class="page-btn">처음으로</button></a>
            <a href="?page=<?= $page - 1 ?><?= $queryStr ?>"><button class="page-btn">이전으로</button></a>
        <?php else: ?>
            <button class="page-btn disabled">처음으로</button>
            <button class="page-btn disabled">이전으로</button>
        <?php endif; ?>
        <?php
        // 숫자 페이지 버튼
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $page) {
                echo "<span class='current'>$i</span>";
            } else {
                echo "<a href='?page=$i$queryStr'>$i</a>";
            }
        }

        // > 버튼
        if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?><?= $queryStr ?>"><button class="page-btn">다음으로</button></a>
            <a href="?page=<?= $totalPages ?><?= $queryStr ?>"><button class="page-btn">맨뒤로</button></a>
        <?php else: ?>
            <button class="page-btn disabled">다음으로</button>
            <button class="page-btn disabled">맨뒤로</button>
        <?php endif; ?>
    </div>
</body>
</html>