<?php
require_once 'config.php';

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // First get the image path
    $stmt = $pdo->prepare("SELECT image_path FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Delete the image file
        $imagePath = "uploads/" . $user['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        // Delete the record
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    header('Location: index.php');
    exit();
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc3f7;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --border-radius: 6px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            background-color: #f5f7fa;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        h1 {
            color: var(--secondary-color);
            margin-bottom: 20px;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #f1f3f5;
        }
        
        .profile-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--accent-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .actions {
            white-space: nowrap;
        }
        
        .action-link {
            color: var(--primary-color);
            text-decoration: none;
            margin-right: 10px;
            transition: color 0.3s;
        }
        
        .action-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .no-users {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
            
            .header-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Management System</h1>
        
        <div class="header-actions">
            <a href="create.php" class="btn">Add New User</a>
        </div>
        
        <?php if (count($users) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Profile Picture</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <?php if ($user['image_path']): ?>
                            <img src="uploads/<?= htmlspecialchars($user['image_path']) ?>" alt="Profile Picture" class="profile-img">
                        <?php else: ?>
                            <span class="no-image">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('M j, Y g:i A', strtotime(htmlspecialchars($user['created_at']))) ?></td>
                    <td class="actions">
                        <a href="index.php?delete=<?= $user['id'] ?>" class="action-link btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="no-users">
                <p>No users found. Would you like to <a href="create.php">add a new user</a>?</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>