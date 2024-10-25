<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header('Location: UserLogin.php'); // Redirect to user login if not logged in
    exit();
}

// Include database connection
include 'Database.php';

// Fetch current user details
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$currentUser = $result->fetch_assoc();

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Update query
    $sqlUpdate = "UPDATE users SET name = ?, email = ?";
    
    // If password is provided, hash it and update
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sqlUpdate .= ", password = ?";
    }

    $sqlUpdate .= " WHERE id = ?";
    
    $stmtUpdate = $conn->prepare($sqlUpdate);
    
    if (!empty($password)) {
        $stmtUpdate->bind_param("sssi", $name, $email, $hashedPassword, $userId);
    } else {
        $stmtUpdate->bind_param("ssi", $name, $email, $userId);
    }
    
    $stmtUpdate->execute();

    // Redirect to the same page after update
    header('Location: user_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            display: flex;
        }

        .sidebar {
            background-color: #28a745;
            color: white;
            width: 200px;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #218838;
        }

        .content {
            margin-left: 220px;
            padding: 20px;
            flex-grow: 1;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            color: #333;
        }

        header nav {
            display: flex;
            align-items: center;
        }

        header nav a {
            margin: 0 15px;
            color: #28a745;
            text-decoration: none;
        }

        .print-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 400px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>

<div class="sidebar">
    <h2>User Panel</h2>
    <a href="user_dashboard.php">Dashboard</a>
    <a href="user_logout.php">Logout</a>
</div>

<div class="content">
    <header>
        <h1>User Dashboard</h1>
        <nav>
            <button class="print-button" onclick="printPage()">Print Profile</button>
            <a href="user_logout.php">Logout</a>
        </nav>
    </header>

    <h2>Your Profile</h2>
    
    <form method="post">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($currentUser['name']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>

        <label for="password">New Password (leave blank to keep current):</label>
        <input type="password" name="password" id="password" placeholder="Enter new password">

        <input type="submit" value="Update Profile">
    </form>

</div>

</body>
</html>
