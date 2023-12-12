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

<html>
  <head>
    <meta charset="utf-8" />
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-blue-100 flex items-center justify-center p-24">
    <div
      class="bg-gray-100 p-6 w-full h-full rounded-lg flex items-center justify-center flex-col text-center shadow-lg">
      <small class="text-base font-medium tracking-widest text-gray-400"
        >RIZAL TECHNOLOGICAL UNIVERSITY</small
      >
      <h1 class="text-8xl font-bold tracking-tight w-1/2 leading-[5rem]">
        Dormitory Management System
      </h1>
      <p class="mt-12">Welcome, <?php echo $_SESSION['name']; ?>!
            <?php if (isset($message)) : ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?></p>
      <div class="flex gap-2 mt-6">
         <button name="run_face_auth" onclick="enrollNewUser()"  class="px-6 py-2 bg-blue-700 rounded-full text-gray-100 hover:bg-blue-900">Run Face Auth</button>
        <form action="faceauth.php" method="post"  class="px-6 py-2 bg-blue-700 rounded-full text-gray-100 hover:bg-blue-900 m-0">
        <button type="submit" name="start_over" >Start Over</button>
        </form>
      </div>
    </div>
    
<script src="https://cdn.faceio.net/fio.js"></script>
    <script type="text/javascript">
        // Instantiate fio.js with your application Public ID
        const faceio = new faceIO("fioa2fe5");
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