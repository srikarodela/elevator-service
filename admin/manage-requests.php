<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config/db.php';

$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'] ?? '';
    $status = $_POST['status'] ?? '';

    if (!empty($request_id) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE service_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);
        
        if ($stmt->execute()) {
            $message = '<div class="success-message">Status updated successfully!</div>';
        } else {
            $message = '<div class="error-message">Error updating status. Please try again.</div>';
        }
        $stmt->close();
    }
}

// Handle delete
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $message = '<div class="success-message">Request deleted successfully!</div>';
    } else {
        $message = '<div class="error-message">Error deleting request. Please try again.</div>';
    }
    $stmt->close();
}

// Fetch service requests
$requests = [];
$result = $conn->query("SELECT * FROM service_requests ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

// Get statistics
$total_requests = count($requests);
$pending_count = 0;
$in_progress_count = 0;
$completed_count = 0;

foreach ($requests as $request) {
    switch ($request['status']) {
        case 'Pending':
            $pending_count++;
            break;
        case 'In Progress':
            $in_progress_count++;
            break;
        case 'Completed':
            $completed_count++;
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Service Requests - Elevator Services</title>
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
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card.total { border-top: 4px solid #3498db; }
        .stat-card.total h3 { color: #3498db; }
        
        .stat-card.pending { border-top: 4px solid #f39c12; }
        .stat-card.pending h3 { color: #f39c12; }
        
        .stat-card.progress { border-top: 4px solid #e74c3c; }
        .stat-card.progress h3 { color: #e74c3c; }
        
        .stat-card.completed { border-top: 4px solid #27ae60; }
        .stat-card.completed h3 { color: #27ae60; }
        
        .requests-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .requests-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .requests-table th,
        .requests-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .requests-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .requests-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-select {
            padding: 0.5rem;
            border: 2px solid #ddd;
            border-radius: 3px;
            background: white;
            cursor: pointer;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-delete {
            padding: 0.5rem 1rem;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.875rem;
            transition: background 0.3s ease;
        }
        
        .btn-delete:hover {
            background: #c0392b;
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
        
        .problem-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .problem-text:hover {
            white-space: normal;
            word-wrap: break-word;
        }
        
        @media (max-width: 768px) {
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
            
            .requests-table th,
            .requests-table td {
                padding: 0.5rem;
                font-size: 0.875rem;
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
                    <li><a href="manage-requests.php" class="active">Service Requests</a></li>
                    <li><a href="manage-modules.php">Modules</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <div class="admin-content container">
            <?php echo $message; ?>
            
            <div class="stats-row">
                <div class="stat-card total">
                    <h3><?php echo $total_requests; ?></h3>
                    <p>Total Requests</p>
                </div>
                <div class="stat-card pending">
                    <h3><?php echo $pending_count; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="stat-card progress">
                    <h3><?php echo $in_progress_count; ?></h3>
                    <p>In Progress</p>
                </div>
                <div class="stat-card completed">
                    <h3><?php echo $completed_count; ?></h3>
                    <p>Completed</p>
                </div>
            </div>

            <div class="requests-table">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Location</th>
                                <th>Elevator Type</th>
                                <th>Problem</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($requests)): ?>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td><?php echo $request['id']; ?></td>
                                        <td><?php echo htmlspecialchars($request['name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($request['email']); ?></td>
                                        <td><?php echo htmlspecialchars($request['location']); ?></td>
                                        <td><?php echo htmlspecialchars($request['elevator_type']); ?></td>
                                        <td>
                                            <div class="problem-text" title="<?php echo htmlspecialchars($request['problem']); ?>">
                                                <?php echo htmlspecialchars(substr($request['problem'], 0, 50)) . '...'; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" action="manage-requests.php" style="display: inline;">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <select name="status" class="status-select" onchange="this.form.submit()">
                                                    <option value="Pending" <?php echo $request['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="In Progress" <?php echo $request['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                    <option value="Completed" <?php echo $request['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="manage-requests.php?delete=<?php echo $request['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" style="text-align: center;">No service requests found.</td>
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
