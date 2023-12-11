<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="dashboard.php"><i class="fas fa-user-circle"></i>Dashboard</a>
				<a href="rooms.php"><i class="fas fa-user-circle"></i>Rooms</a>
				<a href="students.php"><i class="fas fa-user-circle"></i>Students</a>
				<a href="accounts.php"><i class="fas fa-user-circle"></i>Accounts</a>
				<a href="#"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
		</div>

		<div class="totalrooms">Total Rooms <label for="">5</label></div>
		<div class="regstudents">Registered Students<label for="">5</label></div>
		<div class="studunpaid">Student Unpaid <label for="">5</label></div>
		<div class="settacc">Settled Accounts <label for="">5</label></div>
		<div class="guests">Guests <label for="">5</label></div>

	</body>

</html>