<?php
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['studnum'], $_POST['school'])) {
    // Could not get the data that should have been sent.
    exit('Please complete the registration form!');
}

// Make sure the submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email']) || empty($_POST['studnum']) || empty($_POST['school'])) {
    // One or more values are empty.
    exit('Please complete the registration form');
}

// Check if the email domain is "rtu.edu.ph"
$emailDomain = explode('@', $_POST['email']);
if (end($emailDomain) !== 'rtu.edu.ph') {
    exit('Only email addresses with the domain "rtu.edu.ph" are allowed!');
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, password FROM tenants WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        exit('Email is not valid!');
    }
    if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
        exit('Username is not valid!');
    }

    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    // Store the result so we can check if the account exists in the database.
    if ($stmt->num_rows > 0) {
        // Username already exists
        echo 'Username exists, please choose another!';
    } else {
        // Username doesn't exist, insert new account
        // Check email domain again before inserting
        if (end($emailDomain) === 'rtu.edu.ph') {
            if ($stmt = $con->prepare('INSERT INTO tenants (username, password, email, studentid, school) VALUES (?, ?, ?, ?, ?)')) {
                // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param('sssss', $_POST['username'], $password, $_POST['email'], $_POST['studnum'], $_POST['school']);
                $stmt->execute();

                // Automatic login after registration
                session_start();
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $stmt->insert_id; // Assuming 'id' is your auto-increment primary key

                header('Location: faceauth.php'); // Redirect to faceauth.php after successful registration and login
            } else {
                // Something is wrong with the SQL statement, so you must check to make sure your tenants table exists with all five fields.
                echo 'Could not prepare statement!';
            }
        } else {
            exit('Only email addresses with the domain "rtu.edu.ph" are allowed!');
        }
    }

    $stmt->close();
} else {
    // Something is wrong with the SQL statement, so you must check to make sure your tenants table exists with all 3 fields.
    echo 'Could not prepare statement!';
}

$con->close();
?>
