<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>로그인 | SANDEUL</title>
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
        button:hover{
            background-color: #1565c0;
        }
        p:hover{
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1 style="margin-top: 160px;">로그인</h1>
    <form action="login_action.php" method="POST">
        <input type="text" name="userid" placeholder="아이디" required>
        <input type="password" name="userpw" placeholder="비밀번호" required>
        <button type = "submit">로그인</button>
        <p style="text-align: center; margin-top: -5px;"><a href="find_pw.php" style="text-decoration: none; color: #fff;">비밀번호를 잊으셨나요?</a></p>
    </form>
</body>
</html>