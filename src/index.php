<?php
session_start();
$loggedIn = isset($_SESSION['userid']);
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="ko">
<haed>
	<meta charset="UTF-8">
	<title>SANDEUL</title>
	<style>
		body {
			margin: 0;
			background-color: #121212;
			color: white;
			font-family: Arial, sans-serif;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			height: 100vh;
		}
		.logo {
			font-size: 48px;
			font-weight: bold;
			margin-bottom: 30px;
		}
		.btn-container {
			display:flex;
			gap: 10px;
			width: 230px;
		}
		.btn {
			flex: 1;
			width: 100%;
			padding: 5px 0px;
			text-align: center;
			font-size: 16px;
			background-color: #1e88e5;
			color: white;
			border: none;
			border-radius: 8px;
			cursor: pointer;
			transition: backgroud-color 0.3s;
			box-sizing: border-box;
			text-decoration: none;
		}
		.btn:hover {
			background-color: #1565c0;
		}
		.btn2-container {
			flex-direction: column;
			display:flex;
			gap:5px;
			width: 230px;
		}
		.btn2 {
			width: 100%;
			height: 30px;
			font-size: 16px;
			background-color: #1e88e5;
			color: white;
			border: none;
			border-radius: 8px;
			cursor: pointer;
			transition: backgroud-color 0.3s;
		}
	</style>
</head>
<body>
	<div class="logo">SANDEUL</div>

	<?php if ($loggedIn): ?>
		<p><?= htmlspecialchars($username) ?>님 환영합니다!</p>
		<div class="btn2-container">
			<a href = "board.php"><button class = "btn2">게시판</button></a>
			<a href = "chat.php"><button class = "btn2">심심이(구현중)</button></a>
			<a href = "mypage.php"><button class = "btn2">마이페이지</button></a>
			<a href = "logout.php"><button class = "btn2">로그아웃</button></a>
		</div>
	<?php else: ?>
		<div class="btn-container">
			<a href = "login.php" class = "btn">로그인</a>
			<a href = "register.php" class = "btn">회원가입</a>
		</div>
	<?php endif; ?>
</body>
</html>