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
// Fetch all rows from the 'tenants' table
$result = mysqli_query($con, 'SELECT * FROM tenants');

// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
$stmt = $con->prepare('SELECT password, email FROM tenants WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
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
		</nav>
		<div class="content">
			<h2>Current Students</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?=$_SESSION['name']?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><?=$password?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
				</table>
			</div>

			<h2>All Tenants</h2>
			<div>
				<!-- Display the 'tenants' table -->
				<table border="1">
					<tr>
						<th>ID</th>
						<th>Username</th>
						<th>Password</th>
						<th>Email</th>
						<!-- Add more columns as needed -->
					</tr>
					<?php
					while ($row = mysqli_fetch_assoc($result)) {
						echo "<tr>";
						echo "<td>{$row['id']}</td>";
						echo "<td>{$row['username']}</td>";
						echo "<td>{$row['password']}</td>";
						echo "<td>{$row['email']}</td>";
						// Add more columns as needed
						echo "</tr>";
					}
					?>
				</table>
			</div>
		</div>
	</body>
</html>
