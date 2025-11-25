<?php
session_start();
include 'db.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>비밀번호 찾기 | SANDEUL</title>
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 140px;
            
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
        button:hover{
            background-color: #1565c0;
        }
    </style>
</head>
<body>
    <h2>비밀번호 찾기</h2>
    <p>아이디와 이름을 입력해주세요.</p>
  <form action="find_pw_action.php" method="post">
        <input type="text" name="userid" placeholder="아이디" required>
        <input type="text" name="username" placeholder="이름" required>
    <button type="submit">비밀번호 찾기</button>
  </form>
</body>
</html>