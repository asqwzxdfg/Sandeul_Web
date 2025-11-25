<?php
include 'db.php';
session_start();

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
    exit;
}

$userid = $_SESSION['userid'];
$stmt = $mysqli->prepare("SELECT id, userid, username, created_at FROM users WHERE userid = ?");
$stmt->bind_param("s", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>마이페이지</title>
  <style>
    body { background: #111; color: #fff; font-family: sans-serif; }
    .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #222; border-radius: 10px; }
    .row { margin-bottom: 15px; }
    .label { display: block; font-weight: bold; margin-bottom: 10px; }
    input[type="text"] { width: 97%; padding: 8px; background: #333; color: #fff; border: none; border-radius: 5px; font-size: 16px;}
    .button-container {text-align: center;}
    button { padding: 10px 15px; border: none; border-radius: 5px; background: #08f; color: #fff; cursor: pointer; margin-top: 10px;}
    .button2 { padding: 0px 68px; border: none; border-radius: 5px; background: #08f; color: #fff; cursor: pointer; margin-top: 5px;}
    .pw-change { display: none; margin-top: 20px; }
    .back-link { margin-top: 30px; color: #90caf9; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="container">
    <h2>마이페이지</h2>
    <form action="mypage_action.php" method="post">
      <input type="hidden" name="id" value="<?= $user['id'] ?>">
      
      <div class="row">
        <div class="label">아이디</div>
        <input type="text" value="<?= $user['userid'] ?>" disabled>
      </div>

      <div class="row">
        <div class="label">이름</div>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">
      </div>

      <div class="row">
        <div class="label">가입일</div>
        <input type="text" value="<?= $user['created_at'] ?>" disabled>
      </div>
      <div class="button-container">
        <button type="button" onclick="togglePwChange()">비밀번호 변경</button>
        <button type="submit">수정 완료</button>
        <button type="button" onclick="location.href='index.php'"> 메인으로 </button>
      </div>
    </form>

    <!-- 비밀번호 변경 영역 -->
    <div class="pw-change" id="pwChangeBox">
      <form action="change_pw.php" method="post">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">

        <div class="row">
          <div class="label">현재 비밀번호</div>
          <input type="password" name="current_pw" required>
        </div>

        <div class="row">
          <div class="label">새 비밀번호</div>
          <input type="password" name="new_pw1" required>
        </div>

        <div class="row">
          <div class="label">새 비밀번호 확인</div>
          <input type="password" name="new_pw2" required>
        </div>

        <button type="submit">비밀번호 변경</button>
      </form>
    </div>
  </div>

  <script>
    function togglePwChange() {
      const box = document.getElementById('pwChangeBox');
      box.style.display = box.style.display === 'none' ? 'block' : 'none';
    }
  </script>
</body>
</html>