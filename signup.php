<?php
session_start();

// Include the database connection file (assuming it's named Dbconn.php)
require_once 'Dbconn.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input from the form
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    $Address = $_POST['Address'];

    // You should perform proper validation and sanitation here
    // For simplicity, we'll assume that required fields are not empty

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Handle profile image upload
        $profile_image_name = $_FILES['profile_image']['name'];
        $profile_image_tmp = $_FILES['profile_image']['tmp_name'];
        $profile_image_path = "uploads/" . $profile_image_name; // Adjust the path as needed

        // Move the uploaded file to the desired directory
        move_uploaded_file($profile_image_tmp, $profile_image_path);

        // Prepare and execute the SQL query to insert a new user based on the selected user type
        $query = "INSERT INTO users (Full_Name, email, phone_Number, User_Name, Password, UserType, AccessTime, profile_Image, Address) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?,?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$full_name, $email, $phone_number, $username, $hashed_password, $user_type, $profile_image_path, $Address]);

        // Optionally, you can redirect the user to the login page after signup
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        // Handle database errors
        $error_message = "Error: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <!-- Link to your stylesheet -->
    <link rel="stylesheet" href="CSS/style.css" />
</head>
<body>
<div class="topnav">
        <a href="signup.php">Home</a>     
        
        <div class="topnav-right">
            <a href="signup.php">Sign Up</a>
            <a href="index.php">Sign In</a>
            <!--<a href="logout.php">Log out</a>-->
        </div>
</div>

    <h1>Register here!!</h1>

    <?php if (isset($error_message)) : ?>
        <!-- Display any error messages -->
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>



    <form action="signup.php" method="post" enctype="multipart/form-data">
    <!-- Add the enctype attribute for file uploads -->
    <label for="full_name">Full Name:</label>
    <input type="text" id="full_name" name="full_name" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="phone_number">Phone Number:</label>
    <input type="text" id="phone_number" name="phone_number" required>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <!-- Add a dropdown for selecting user type -->
    <label for="user_type">User Type:</label>
    <select id="user_type" name="user_type" required>
        <option value="Author">Author</option>
        <option value="Administrator">Administrator</option>
        <option value="Super_User">Super User</option>
    </select><br>

    <label for="profile_image">Profile Image:</label>
    <input type="file" id="profile_image" name="profile_image" accept="image/*" required>

    <label for="Address">Address:</label>
    <input type="text" id="Address" name="Address" required><br>

    <button type="submit">Sign Up</button>
</form>
</body>
</html>
