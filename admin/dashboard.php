<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config/db.php';

// Get statistics
$total_requests = 0;
$pending_requests = 0;
$in_progress_requests = 0;
$completed_requests = 0;
$total_modules = 0;

// Get service request statistics
$result = $conn->query("SELECT COUNT(*) as total FROM service_requests");
if ($result) {
    $row = $result->fetch_assoc();
    $total_requests = $row['total'];
}

$result = $conn->query("SELECT COUNT(*) as pending FROM service_requests WHERE status = 'Pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $pending_requests = $row['pending'];
}

$result = $conn->query("SELECT COUNT(*) as in_progress FROM service_requests WHERE status = 'In Progress'");
if ($result) {
    $row = $result->fetch_assoc();
    $in_progress_requests = $row['in_progress'];
}

$result = $conn->query("SELECT COUNT(*) as completed FROM service_requests WHERE status = 'Completed'");
if ($result) {
    $row = $result->fetch_assoc();
    $completed_requests = $row['completed'];
}

// Get modules count
$result = $conn->query("SELECT COUNT(*) as total FROM modules");
if ($result) {
    $row = $result->fetch_assoc();
    $total_modules = $row['total'];
}

// Get recent requests
$recent_requests = [];
$result = $conn->query("SELECT * FROM service_requests ORDER BY created_at DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_requests[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Elevator Services</title>
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
        
        .welcome-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stats-grid {
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
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
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
        
        .stat-card.modules { border-top: 4px solid #9b59b6; }
        .stat-card.modules h3 { color: #9b59b6; }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .dashboard-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .dashboard-section h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .action-btn {
            display: block;
            padding: 1rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .action-btn:hover {
            background: #2980b9;
        }
        
        .recent-requests {
            overflow-x: auto;
        }
        
        .requests-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .requests-table th,
        .requests-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .requests-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.875rem;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-progress {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .admin-nav {
                flex-direction: column;
                gap: 1rem;
            }
            
            .admin-nav ul {
                flex-wrap: wrap;
                justify-content: center;
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
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="manage-requests.php">Service Requests</a></li>
                    <li><a href="manage-modules.php">Modules</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <div class="admin-content container">
            <div class="welcome-section">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
                <p>This is your admin dashboard where you can manage service requests and elevator modules.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card total">
                    <h3><?php echo $total_requests; ?></h3>
                    <p>Total Requests</p>
                </div>
                <div class="stat-card pending">
                    <h3><?php echo $pending_requests; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="stat-card progress">
                    <h3><?php echo $in_progress_requests; ?></h3>
                    <p>In Progress</p>
                </div>
                <div class="stat-card completed">
                    <h3><?php echo $completed_requests; ?></h3>
                    <p>Completed</p>
                </div>
                <div class="stat-card modules">
                    <h3><?php echo $total_modules; ?></h3>
                    <p>Modules</p>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-section">
                    <h2>Quick Actions</h2>
                    <div class="quick-actions">
                        <a href="manage-requests.php" class="action-btn">View All Requests</a>
                        <a href="manage-modules.php" class="action-btn">Manage Modules</a>
                        <a href="manage-modules.php?action=add" class="action-btn">Add New Module</a>
                        <a href="../index.html" class="action-btn">View Website</a>
                    </div>
                </div>

                <div class="dashboard-section">
                    <h2>Recent Service Requests</h2>
                    <div class="recent-requests">
                        <?php if (!empty($recent_requests)): ?>
                            <table class="requests-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_requests as $request): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($request['name']); ?></td>
                                            <td><?php echo htmlspecialchars($request['elevator_type']); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $request['status'])); ?>">
                                                    <?php echo htmlspecialchars($request['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No service requests yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
