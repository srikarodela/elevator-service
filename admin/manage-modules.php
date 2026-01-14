<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config/db.php';

$message = '';
$action = $_GET['action'] ?? '';
$edit_id = $_GET['edit'] ?? '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_module'])) {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $capacity = $_POST['capacity'] ?? '';
        $speed = $_POST['speed'] ?? '';

        if (!empty($title) && !empty($description) && !empty($capacity) && !empty($speed)) {
            $stmt = $conn->prepare("INSERT INTO modules (title, description, capacity, speed) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $description, $capacity, $speed);
            
            if ($stmt->execute()) {
                $message = '<div class="success-message">Module added successfully!</div>';
            } else {
                $message = '<div class="error-message">Error adding module. Please try again.</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="error-message">Please fill in all fields.</div>';
        }
    } elseif (isset($_POST['edit_module'])) {
        $id = $_POST['id'] ?? '';
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $capacity = $_POST['capacity'] ?? '';
        $speed = $_POST['speed'] ?? '';

        if (!empty($id) && !empty($title) && !empty($description) && !empty($capacity) && !empty($speed)) {
            $stmt = $conn->prepare("UPDATE modules SET title = ?, description = ?, capacity = ?, speed = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $title, $description, $capacity, $speed, $id);
            
            if ($stmt->execute()) {
                $message = '<div class="success-message">Module updated successfully!</div>';
                $edit_id = '';
                $action = '';
            } else {
                $message = '<div class="error-message">Error updating module. Please try again.</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="error-message">Please fill in all fields.</div>';
        }
    }
}

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM modules WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $message = '<div class="success-message">Module deleted successfully!</div>';
    } else {
        $message = '<div class="error-message">Error deleting module. Please try again.</div>';
    }
    $stmt->close();
}

// Fetch modules
$modules = [];
$result = $conn->query("SELECT * FROM modules ORDER BY title");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }
}

// Get module data for editing
$edit_module = null;
if ($action == 'edit' && !empty($edit_id)) {
    $stmt = $conn->prepare("SELECT * FROM modules WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $edit_module = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Modules - Elevator Services</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            background: #f4f4f4;
            min-height: 100vh;
        }
        
        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-nav h1 {
            color: #3498db;
            font-size: 1.5rem;
        }
        
        .admin-nav ul {
            display: flex;
            list-style: none;
            gap: 1rem;
            margin: 0;
            padding: 0;
        }
        
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .admin-nav a:hover {
            background: #3498db;
        }
        
        .admin-nav a.active {
            background: #3498db;
        }
        
        .admin-content {
            padding: 2rem;
        }
        
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header h2 {
            color: #2c3e50;
            margin: 0;
        }
        
        .add-btn {
            background: #27ae60;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .add-btn:hover {
            background: #229954;
        }
        
        .modules-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .modules-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .modules-table th,
        .modules-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .modules-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .modules-table tr:hover {
            background: #f8f9fa;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-edit,
        .btn-delete {
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.875rem;
            transition: background 0.3s ease;
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
        }
        
        .btn-edit:hover {
            background: #2980b9;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c0392b;
        }
        
        .form-modal {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn-submit {
            background: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }
        
        .btn-submit:hover {
            background: #2980b9;
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .btn-cancel:hover {
            background: #7f8c8d;
        }
        
        .success-message,
        .error-message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .admin-nav {
                flex-direction: column;
                gap: 1rem;
            }
            
            .admin-nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <nav class="admin-nav container">
                <h1>Admin Panel</h1>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="manage-requests.php">Service Requests</a></li>
                    <li><a href="manage-modules.php" class="active">Modules</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <div class="admin-content container">
            <?php echo $message; ?>
            
            <?php if ($action == 'add' || ($action == 'edit' && $edit_module)): ?>
                <div class="form-modal">
                    <h2><?php echo $action == 'add' ? 'Add New Module' : 'Edit Module'; ?></h2>
                    <form method="POST" action="manage-modules.php">
                        <?php if ($action == 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_module['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="title">Module Title *</label>
                            <input type="text" id="title" name="title" value="<?php echo $edit_module['title'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" rows="4" required><?php echo $edit_module['description'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="capacity">Capacity *</label>
                            <input type="text" id="capacity" name="capacity" value="<?php echo $edit_module['capacity'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="speed">Speed *</label>
                            <input type="text" id="speed" name="speed" value="<?php echo $edit_module['speed'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="<?php echo $action == 'add' ? 'add_module' : 'edit_module'; ?>" class="btn-submit">
                                <?php echo $action == 'add' ? 'Add Module' : 'Update Module'; ?>
                            </button>
                            <a href="manage-modules.php" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
            
            <div class="page-header">
                <h2>Manage Elevator Modules</h2>
                <?php if ($action != 'add' && $action != 'edit'): ?>
                    <a href="manage-modules.php?action=add" class="add-btn">+ Add New Module</a>
                <?php endif; ?>
            </div>

            <div class="modules-table">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Capacity</th>
                                <th>Speed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($modules)): ?>
                                <?php foreach ($modules as $module): ?>
                                    <tr>
                                        <td><?php echo $module['id']; ?></td>
                                        <td><?php echo htmlspecialchars($module['title']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($module['description'], 0, 100)) . '...'; ?></td>
                                        <td><?php echo htmlspecialchars($module['capacity']); ?></td>
                                        <td><?php echo htmlspecialchars($module['speed']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="manage-modules.php?action=edit&edit=<?php echo $module['id']; ?>" class="btn-edit">Edit</a>
                                                <a href="manage-modules.php?delete=<?php echo $module['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this module?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No modules found. <a href="manage-modules.php?action=add">Add your first module</a>.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
