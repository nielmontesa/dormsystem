<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    // Redirect to the login page if not logged in
    header('Location: index.html');
    exit;
}

// Check if the 'start over' button is clicked
if (isset($_POST['start_over'])) {
    // Include your database connection
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'phplogin';

    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
    if (mysqli_connect_errno()) {
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    // Delete the account record
    $stmt = $con->prepare('DELETE FROM managers WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();

    // Destroy the session and redirect to the index page
    session_destroy();
    header('Location: index.html');
    exit;
}

// Check if the 'run face auth' button is clicked
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Authentication</title>
</head>
<body>

<h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1>

<?php if (isset($message)) : ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>
    <button name="run_face_auth" onclick="enrollNewUser()">Run Face Auth</button>
<form action="faceauth.php" method="post">
    <button type="submit" name="start_over">Start Over</button>
</form>

 <script src="https://cdn.faceio.net/fio.js"></script>
    <script type="text/javascript">
        // Instantiate fio.js with your application Public ID
        const faceio = new faceIO("fioaf152");
        function enrollNewUser(){
           // Start the facial enrollment process
           faceio.enroll({
                "locale": "auto", // Default user locale
                enrollIntroTimeout: "4",
            }).then(userInfo => {
                // User Successfully Enrolled!
                alert(
                    `Success! You will now be redirected.`
                );
                window.location.href = "dashboard.php";
                console.log(userInfo);
                // handle success, save the facial ID, redirect to dashboard...
            }).catch(errCode => {
                alert(
                    `Failure! You will need to start over.`
                );
                // handle enrollment failure. Visit:
                // https://faceio.net/integration-guide#error-codes
                // for the list of all possible error codes
            })
        }
    </script>
</body>
</html>