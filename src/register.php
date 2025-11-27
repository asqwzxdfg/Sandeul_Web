<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입 | SANDEUL</title>
    <style>
    body {
      background-color: #121212;
      color: white;
      font-family: sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: 100px;
    }
    form {
      display: flex;
      flex-direction: column;
      width: 300px;
      gap: 15px;
    }
    input {
      padding: 10px;
      border-radius: 5px;
      border: none;
    }
    button {
      padding: 10px;
      background-color: #1e88e5;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #1565c0;
    }
    .back-btn {
        text-align: center;
        margin-top: 20px;
        display: inline-block;
        color: #90caf9;
        text-decoration: none;
        font-size: 14px;
    }
    .back-btn:hover{
        text-decoration: underline;
    }
  </style>
  </head>
  <body>
    <h1>회원가입</h1>
    <form method="POST" action="register_action.php">
        <input type="text" name="userid" placeholder="아이디" required>
        <input type="password" name="userpw" placeholder="비밀번호" required>
        <input type="text" name="username" placeholder="이름" required>
        <button type="submit">가입하기</button>
        <a href="index.php" class="back-btn">메인으로 돌아가기</a>
    </form>
</body>
</html>