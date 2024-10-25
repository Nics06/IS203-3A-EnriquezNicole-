<?php
session_start();

// Check if the user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header('Location: AdminLogin.php'); // Redirect to login if not logged in
    exit();
}

// Include database connection
include 'Database.php';

// Fetching admin user data
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    header('Location: AdminLogin.php');
    exit();
}

// Example: Fetch all users
$sqlUsers = "SELECT * FROM users";
$resultUsers = $conn->query($sqlUsers);

// Fetch user count
$userCount = $resultUsers->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            display: flex;
        }

        .sidebar {
            background-color: #007bff;
            color: white;
            width: 250px;
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
            background-color: #0056b3;
        }

        .content {
            margin-left: 260px;
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

        header nav a {
            margin: 0 15px;
            color: #007bff;
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

        .dashboard-summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .summary-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 30%;
            text-align: center;
        }

        .summary-card h3 {
            margin: 0;
            color: #007bff;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script>
        function printProfile() {
            window.print();
        }
    </script>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="manage_users.php">Manage Users</a>
    <a href="settings.php">Settings</a>
    <a href="Alogout.php">Logout</a>
</div>

<div class="content">
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <nav>
            <button class="print-button" onclick="printProfile()">Print Profile</button>
            <a href="Alogout.php">Logout</a>
        </nav>
    </header>

    <h2>Admin Dashboard</h2>

    <div class="dashboard-summary">
        <div class="summary-card">
            <h3>Total Users</h3>
            <p><?php echo $userCount; ?></p>
        </div>
        <div class="summary-card">
            <h3>Active Users</h3>
            <p><?php echo $userCount; ?></p> <!-- Placeholder: Adjust logic for active users -->
        </div>
        <div class="summary-card">
            <h3>New Users</h3>
            <p>10</p> <!-- Placeholder: Add logic for new users -->
        </div>
    </div>

    <h2>All Users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultUsers->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
