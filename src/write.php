<?php
session_start();
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기 | SANDEUL</title>
      <style>
        body {
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
            font-size: 32px;
            margin-bottom: 20px;
        }

        form {
            width: 500px;
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            margin-bottom: 16px;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            background-color: #1e1e1e;
            color: white;
            font-family: Arial, sans-serif;
        }

        textarea {
            height: 200px;
            resize: vertical;
        }

        .btn {
            padding: 8px;
            background-color: #1e88e5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #1565c0;
        }

        .back-link {
            margin-top: 15px;
            color: #90caf9;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>글쓰기</h1>
    <form action="write_action.php" method="post" enctype="multipart/form-data">
        <label for="title">제목</label>
        <input type="text" name="title" id="title" required>

        <label for="content">내용</label>
        <textarea name="content" id="content" required></textarea>

        <label for="upload_files" style="display: block; margin-top: 20px; font-weight: bold; color: #90caf9;">첨부파일</label>
        <input type="file" name="upload_files[]" id="upload_files" multiple
             style="background-color: #2c2c2c; color: white; border: 1px solid #444; padding: 10px; border-radius: 8px; width: 100%; box-sizing: border-box;">

        <button type="submit" class="btn">작성 완료</button>
    </form>

    <a href="board.php" class="back-link">목록으로</a>
</body>
</html>