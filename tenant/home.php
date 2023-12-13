<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
$stmt = $con->prepare('SELECT password, email, school, studentid FROM tenants WHERE id = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email, $school, $studentid); // Add school and studentid to bind_result
$stmt->fetch();
$stmt->close();
?>

<html>

<head>
	<script src="https://cdn.tailwindcss.com"></script>
	<meta charset="utf-8">
	<title>Home Page</title>
	<link href="style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
		integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
		crossorigin="anonymous" referrerpolicy="no-referrer">
</head>


<body class="loggedin p-12 flex gap-12">
	<nav class="navtop bg-gray-900 text-white p-12 rounded-lg ">
		<div class="flex flex-col gap-5 justify-between	h-full">
			<div>
				<p class="capitalize">
					<?= $_SESSION['name'] ?>
				</p>
				<p class="text-sm text-gray-300">Tenant</p>
			</div>
			<div class="h-full mt-12">
				<ul class="flex flex-col gap-5">
					<li><a href="home.php"
							class="text-gray-100 hover:text-blue-200 flex items-center justify-start gap-2"><i
								class="fa-solid fa-person"></i>Profile</a></li>
					<li><a href="guests/register.html"
							class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
								class="fa-solid fa-people-pulling fa-sm"></i>Add Guest</a></li>
				</ul>
			</div>
			<div class="flex flex-col gap-2">
				<ul>
					<li><a href="logout.php"
							class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
								class="fa-solid fa-right-from-bracket"></i></i>Logout</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="content">
		<p class="text-5xl mb-3">This is your student dashboard!
		</p>
		<div>
			<p class="text-gray-600 mb-5">Your account details are below:</p>
			<table class="bg-gray-200 text-center border-collapse ">
				<tr>
					<td class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Username:</td>
					<td class="border border-gray-600">
						<?= $_SESSION['name'] ?>
					</td>
				</tr>
				<tr>
					<td class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Email:</td>
					<td class="border border-gray-600">
						<?= $email ?>
					</td>
				</tr>
				<tr>
					<td class="bg-gray-700 text-white p-2 px-4 border border-gray-600">School:</td>
					<td class="border border-gray-600">
						<?= $school ?>
					</td>
				</tr>
				<tr>
					<td class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Student Number:</td>
					<td class="border border-gray-600">
						<?= $studentid ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>

</html>