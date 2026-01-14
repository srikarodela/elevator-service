<?php
include 'config/db.php';

// Fetch modules from database
$modules = [];
$result = $conn->query("SELECT * FROM modules ORDER BY title");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elevator Modules - Elevator Services</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <div class="logo">
                    <h1>Elevator Services</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="service.php">Request Service</a></li>
                    <li><a href="modules.php" class="active">Modules</a></li>
                    <li><a href="index.html#contact">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="page-header">
            <div class="container">
                <h2>Elevator Modules</h2>
                <p>Explore our range of elevator solutions for different applications</p>
            </div>
        </section>

        <section class="modules-section">
            <div class="container">
                <div class="modules-grid">
                    <?php if (!empty($modules)): ?>
                        <?php foreach ($modules as $module): ?>
                            <div class="module-card">
                                <div class="module-header">
                                    <h3><?php echo htmlspecialchars($module['title']); ?></h3>
                                </div>
                                <div class="module-content">
                                    <p class="module-description"><?php echo htmlspecialchars($module['description']); ?></p>
                                    
                                    <div class="module-specs">
                                        <div class="spec-item">
                                            <span class="spec-label">Capacity:</span>
                                            <span class="spec-value"><?php echo htmlspecialchars($module['capacity']); ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <span class="spec-label">Speed:</span>
                                            <span class="spec-value"><?php echo htmlspecialchars($module['speed']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="module-features">
                                        <?php
                                        // Add specific features based on module type
                                        $features = [];
                                        switch ($module['title']) {
                                            case 'Passenger Lift':
                                                $features = ['Smooth ride', 'Energy efficient', 'Modern design', 'Safety sensors'];
                                                break;
                                            case 'Goods Lift':
                                                $features = ['Heavy duty', 'Durable construction', 'Easy loading', 'Low maintenance'];
                                                break;
                                            case 'Hospital Lift':
                                                $features = ['Stretcher compatible', 'Hygienic design', 'Quiet operation', 'Emergency backup'];
                                                break;
                                            case 'Home Lift':
                                                $features = ['Compact design', 'Space saving', 'Easy installation', 'Quiet operation'];
                                                break;
                                            default:
                                                $features = ['Reliable performance', 'Safety certified', 'Low maintenance', 'Energy efficient'];
                                        }
                                        ?>
                                        <ul>
                                            <?php foreach ($features as $feature): ?>
                                                <li><?php echo htmlspecialchars($feature); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="module-footer">
                                    <a href="service.php?elevator_type=<?php echo urlencode($module['title']); ?>" class="btn btn-primary">Request Service</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-modules">
                            <p>No elevator modules available at the moment. Please check back later.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="module-info">
            <div class="container">
                <h3>Why Choose Our Elevator Modules?</h3>
                <div class="info-grid">
                    <div class="info-card">
                        <h4>Quality Assurance</h4>
                        <p>All our elevator modules meet international safety and quality standards.</p>
                    </div>
                    <div class="info-card">
                        <h4>Custom Solutions</h4>
                        <p>We can customize elevator modules to meet your specific requirements.</p>
                    </div>
                    <div class="info-card">
                        <h4>Expert Support</h4>
                        <p>Our team provides comprehensive support from installation to maintenance.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Contact Information</h3>
                    <p><strong>Phone:</strong> +1-234-567-8900</p>
                    <p><strong>Email:</strong> info@elevatorservices.com</p>
                    <p><strong>Address:</strong> 123 Main Street, City, State 12345</p>
                </div>
                <div class="footer-section">
                    <h3>Business Hours</h3>
                    <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
                    <p>Saturday: 9:00 AM - 4:00 PM</p>
                    <p>Sunday: Emergency Services Only</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="service.php">Request Service</a></li>
                        <li><a href="modules.php">Modules</a></li>
                        <li><a href="admin/login.php">Admin Login</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Elevator Services. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
