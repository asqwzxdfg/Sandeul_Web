<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

$id = $_GET['id'];
$loggedInUserId = $_SESSION['userid'] ?? null;

$stmt = $mysqli->prepare("SELECT title, content, userid FROM board WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title,$content,$postUserId);
$stmt->fetch();
$stmt->close();

$file_stmt = $mysqli->prepare("SELECT * FROM board_files WHERE board_id = ?");
$file_stmt->bind_param("i", $id);
$file_stmt->execute();
$file_result = $file_stmt->get_result();

if($loggedInUserId !== $postUserId) {
    echo "<script>alert('작성자만 수정할 수 있습니다.'); history.back()</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글 수정 | SANDEUL</title>
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
        }

        textarea {
            height: 200px;
            resize: vertical;
            font-family: Arial, sans-serif;
        }

        .btn {
            padding: 8px;
            background-color: #1e88e5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
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
        .delete-btn{
            margin-left: 10px; 
            font-size: 12px; 
            background-color: #ef5350; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            padding: 4px 8px; 
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>글 수정</h1>
    <form action="edit_action.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label for="title">제목</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>

        <label for="content">내용</label>
        <textarea name="content" rows="10" required><?= htmlspecialchars($content) ?></textarea>
    
        <div id="file-list" style="margin-top: 20px;">
        <strong style="color: #90caf9;">첨부파일</strong>
        <input type="file" id="hidden-file-input" style="display:none;" multiple>
        <button type="button" onclick="triggerFileSelect()"
                style=" background-color: #1e88e5; color: white; border: none; padding: 4px 8px; border-radius: 6px; cursor: pointer; margin-left: 10px;"> 추가 </button>
            <ul style="list-style: none; padding-left: 0;">
            <?php while ($file = $file_result->fetch_assoc()): ?>
                <li id="file-<?= $file['id'] ?>" style="margin-bottom: 15px;">
                    <a href="<?= htmlspecialchars($file['file_path']) ?>" download style="color: #fff; text-decoration: none;">
                        <?= htmlspecialchars($file['file_name']) ?>
                    <button type = "button" class="delete-btn" data-file-id="<?= $file['id'] ?>">삭제</button>
                    </a>
            </li>
            <?php endwhile; ?>
            </ul>
            <ul id="new-files" style="list-style: none; padding-left: 0;"></ul>
        </div>

        <button type="submit" class="btn">수정완료</button>
    </form>
    
    <a href="view.php?id=<?= $id ?>" class="back-link">뒤로가기</a>
</body>
<script>
function triggerFileSelect() {
    document.getElementById('hidden-file-input').click();
}

document.getElementById('hidden-file-input').addEventListener('change', function(event) {
    const fileList = event.target.files;
    const displayArea = document.getElementById('new-files');
    const form = document.querySelector('form');

    for (let i = 0; i < fileList.length; i++) {
        const file = fileList[i];

        // 새 input 생성
        const realInput = document.createElement('input');
        realInput.type = 'file';
        realInput.name = 'upload_files[]';
        realInput.style.display = 'none';

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        realInput.files = dataTransfer.files;

        // 고유 ID 설정 (삭제할 때 찾기 위함)
        const uid = 'new-file-' + Math.random().toString(36).substring(2, 10);
        realInput.dataset.uid = uid;
        form.appendChild(realInput);

        // 보여질 파일 목록 항목 생성
        const li = document.createElement('li');
        li.id = uid;
        li.style = "margin-bottom: 15px;";

        const span = document.createElement('span');
        span.textContent = file.name;
        span.style = "color: #fff; font-size: 16px;";
        li.appendChild(span);

        // 삭제 버튼 추가
        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = "삭제";
        deleteBtn.style = "margin-left: 15px; font-size: 12px; background-color: #ef5350; color: white; border: none; border-radius: 5px; padding: 4px 8px; cursor: pointer;";
        deleteBtn.onclick = function () {
            li.remove();
            realInput.remove(); // form 내 input도 제거
        };
        li.appendChild(deleteBtn);

        displayArea.appendChild(li);
    }

    this.value = '';
});

document.querySelectorAll('.delete-btn').forEach(button => {
  button.addEventListener('click', function () {
    if (!confirm('정말 이 파일을 삭제하시겠습니까?')) return;

    const fileItem = this.closest('.file-item');
    const fileId = fileItem.dataset.fileId;

    fetch('delete_file.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'file_id=' + encodeURIComponent(fileId)
    })
    .then(res => res.text())
    .then(response => {
      if (response.trim() === 'success') {
        fileItem.remove(); // 화면에서 지우기
      } else {
        alert('파일 삭제 실패: ' + response);
      }
    });
  });
});
</script>
</html>
