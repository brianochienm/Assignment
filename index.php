<?php
session_start();

// Assuming you have a connection.php file
require_once 'Dbconn.php';

// Function to redirect users based on their roles
function redirectUser($userType) {
    switch ($userType) {
        case 'Super_User':
            header('Location: superuser1.php');
            break;
        case 'Administrator':
            header('Location: admin1.php');
            break;
        case 'Author':
            header('Location: author1.php');
            break;
        default:
            // Handle default case or redirect to a generic page
            header('Location: index.php');
            break;
    }
}

// Check if the user is already logged in
if (isset($_SESSION['userId'])) {
    // Redirect to the appropriate page based on the user's role
    redirectUser($_SESSION['userType']);
}

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Perform login validation based on your database
    $query = "SELECT * FROM users WHERE User_Name = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Verify the password
        if (password_verify($password, $result['Password'])) {
            // Set session variables
            $_SESSION['userId'] = $result['userId'];
            $_SESSION['userType'] = $result['UserType'];

            // Redirect to the appropriate page based on the user's role
            redirectUser($result['UserType']);
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Invalid username";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/style.css" />
</head>
<body>
<?php require "navigation.php"; ?>
    <h1>Login</h1>
    
    <?php if (isset($error)) : ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="index.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Sign In</button>
    </form>
</body>
</html>
