<?php
session_start();
include 'Database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query
    $sql = "SELECT * FROM users WHERE email = ? AND role = 'User'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Store user information in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: user_dashboard.php'); // Redirect to user dashboard
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with this email or not a User.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h2 {
            color: #333;
        }

        form {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="email"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #28a745; /* Change to green */
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .error {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 20px;
        }

        p {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>User Login</h2>

<?php if (isset($error)): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form action="UserLogin.php" method="POST">
    <label>Email: </label><input type="email" name="email" required><br>
    <label>Password: </label><input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>

<p>Not a user? <a href="AdminLogin.php">Switch to Admin Login</a>.</p>
<p>Don't have an account? <a href="Index.php">Register here</a>.</p> <!-- New Registration Link -->


</body>
</html>
