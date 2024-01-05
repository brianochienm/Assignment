<?php
session_start();

require_once 'Dbconn.php';

// Check if the user is logged in and is an Author
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'Author') {
    header('Location: index.php');
    exit();
}

$errors = [];
$successMessage = '';

// Fetch existing user data for pre-filling the form
$query = "SELECT Full_Name, email, phone_Number, User_Name, UserType, Address FROM users WHERE userId = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['userId']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['updateProfile'])) {
        // Sanitize and validate input fields
        $newFullName = filter_input(INPUT_POST, 'newFullName', FILTER_SANITIZE_STRING);
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        $newEmail = filter_input(INPUT_POST, 'newEmail', FILTER_SANITIZE_EMAIL);
        $newPhone_Number = filter_input(INPUT_POST, 'newPhone_Number', FILTER_SANITIZE_STRING);
        $newUser_Name = filter_input(INPUT_POST, 'newUser_Name', FILTER_SANITIZE_STRING);
        $newAddress = filter_input(INPUT_POST, 'newAddress', FILTER_SANITIZE_STRING);

        // Validate Full Name
        if (empty($newFullName)) {
            $errors[] = 'Full Name is required';
        }

        // Validate Password
        if (!empty($newPassword)) {
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Password and Confirm Password do not match';
            }
        }

        // If no errors, update the profile
        if (empty($errors)) {
            try {
                // Prepare and execute the update query
                $updateQuery = "UPDATE users SET Full_Name = ?, Password = ?, email = ?, phone_Number = ?, User_Name = ?, Address = ? WHERE userId = ?";
                $updateStmt = $pdo->prepare($updateQuery);

                // Hash the password before updating (if provided)
                $hashedPassword = empty($newPassword) ? $_SESSION['password'] : password_hash($newPassword, PASSWORD_DEFAULT);

                $updateStmt->execute([$newFullName, $hashedPassword, $newEmail, $newPhone_Number, $newUser_Name, $newAddress, $_SESSION['userId']]);
                $updateStmt->closeCursor();

                // Update session data if the password is changed
                if (!empty($newPassword)) {
                    $_SESSION['password'] = $hashedPassword;
                }

                $successMessage = 'Profile updated successfully';
            } catch (PDOException $e) {
                $errors[] = 'Error updating profile: ' . $e->getMessage();
            }
        }
    }
}

// Include the HTML form for updating the profile
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <style>
        /* Floating message style */
        .floating-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            display: none; /* Hide by default */
        }
    </style>
    <script>
        // JavaScript to show and hide the floating message
        document.addEventListener('DOMContentLoaded', function() {
            var successMessage = '<?php echo $successMessage; ?>';
            if (successMessage !== '') {
                var floatingMessage = document.getElementById('floatingMessage');
                floatingMessage.textContent = successMessage;
                floatingMessage.style.display = 'block';

                // Hide the message after 3 seconds
                setTimeout(function() {
                    floatingMessage.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</head>
<body>
    <div class="topnav"> 
        <a href="index.php">Home</a>  
        <div class="topnav-right">
            <a href="author1.php">Back</a>
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <h2>Update Profile</h2>

    <?php
    // Display success message or errors
    if (!empty($successMessage)) {
        echo '<p class="success-message">' . $successMessage . '</p>';
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p class="error-message">' . $error . '</p>';
        }
    }
    ?>

    <!-- Update Profile Form -->
    <form action="update_profile2.php" method="post">
        <label for="newFullName">New Full Name:</label>
        <input type="text" id="newFullName" name="newFullName" value="<?= htmlspecialchars($userData['Full_Name']) ?>" required>

        <label for="newEmail">Email:</label>
        <input type="text" id="newEmail" name="newEmail" value="<?= htmlspecialchars($userData['email']) ?>" required>

        <label for="newPhone_Number">Phone Number:</label>
        <input type="text" id="newPhone_Number" name="newPhone_Number" value="<?= htmlspecialchars($userData['phone_Number']) ?>" required>

        <label for="newUser_Name">User Name:</label>
        <input type="text" id="newUser_Name" name="newUser_Name" value="<?= htmlspecialchars($userData['User_Name']) ?>" required>

        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword">

        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" id="confirmPassword" name="confirmPassword">

        <label for="newAddress">Address:</label>
        <input type="text" id="newAddress" name="newAddress" value="<?= htmlspecialchars($userData['Address']) ?>" required>

        <button type="submit" name="updateProfile">Update Profile</button>
    </form>

    <!-- Floating message -->
    <div id="floatingMessage" class="floating-message"></div>
</body>
</html>
