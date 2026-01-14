<?php
session_start();
include 'config/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $location = $_POST['location'] ?? '';
    $elevator_type = $_POST['elevator_type'] ?? '';
    $problem = $_POST['problem'] ?? '';

    if (!empty($name) && !empty($phone) && !empty($email) && !empty($location) && !empty($elevator_type) && !empty($problem)) {
        $stmt = $conn->prepare("INSERT INTO service_requests (name, phone, email, location, elevator_type, problem) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $phone, $email, $location, $elevator_type, $problem);
        
        if ($stmt->execute()) {
            $message = '<div class="success-message">Service request submitted successfully! We will contact you soon.</div>';
        } else {
            $message = '<div class="error-message">Error submitting request. Please try again.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="error-message">Please fill in all required fields.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Service - Elevator Services</title>
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
                    <li><a href="service.php" class="active">Request Service</a></li>
                    <li><a href="modules.php">Modules</a></li>
                    <li><a href="index.html#contact">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="page-header">
            <div class="container">
                <h2>Request Service</h2>
                <p>Fill out the form below to request elevator service</p>
            </div>
        </section>

        <section class="service-form">
            <div class="container">
                <div class="form-container">
                    <?php echo $message; ?>
                    
                    <form method="POST" action="service.php" class="service-request-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="location">Location *</label>
                                <input type="text" id="location" name="location" placeholder="Building Address" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="elevator_type">Elevator Type *</label>
                            <select id="elevator_type" name="elevator_type" required>
                                <option value="">Select Elevator Type</option>
                                <option value="Passenger Lift">Passenger Lift</option>
                                <option value="Goods Lift">Goods Lift</option>
                                <option value="Hospital Lift">Hospital Lift</option>
                                <option value="Home Lift">Home Lift</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="problem">Problem Description *</label>
                            <textarea id="problem" name="problem" rows="5" placeholder="Please describe the issue you're experiencing with the elevator..." required></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                            <button type="reset" class="btn btn-secondary">Clear Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="service-info">
            <div class="container">
                <h3>Service Information</h3>
                <div class="info-grid">
                    <div class="info-card">
                        <h4>Emergency Services</h4>
                        <p>Available 24/7 for emergency elevator repairs and breakdowns.</p>
                    </div>
                    <div class="info-card">
                        <h4>Response Time</h4>
                        <p>We typically respond to service requests within 2-4 hours.</p>
                    </div>
                    <div class="info-card">
                        <h4>Service Areas</h4>
                        <p>We cover all major cities and surrounding areas.</p>
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
