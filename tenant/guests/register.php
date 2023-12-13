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
if (!isset($_POST['username'], $_POST['email'], $_POST['time'], $_POST['contactnumber'])) {
    // Could not get the data that should have been sent.
    exit('Please complete the registration form!');
}

// Make sure the submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['time']) || empty($_POST['contactnumber'])) {
    // One or more values are empty.
    exit('Please complete the registration form');
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id FROM guests WHERE guest_name = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc)
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
        if ($stmt = $con->prepare('INSERT INTO guests (id, guest_name, email, contact_number, checkin_time) VALUES (NULL, ?, ?, ?, ?)')) {
            $stmt->bind_param('ssss', $_POST['username'], $_POST['email'], $_POST['time'], $_POST['contactnumber']);
            $stmt->execute();

            echo '<script>alert("Guest successfully added"); window.location.href = "register.html";</script>';

        } else {
            // Something is wrong with the SQL statement, so you must check to make sure your guests table exists with all three fields.
            echo 'Could not prepare statement!';
        }
    }

    $stmt->close();
} else {
    // Something is wrong with the SQL statement, so you must check to make sure your guests table exists with all 3 fields.
    echo 'Could not prepare statement!';
}

$con->close();
?>