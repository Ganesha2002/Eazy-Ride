<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Get Bike Details from URL (e.g., booking.php?bike=Urban+Scooter&price=15)
$bike_name = isset($_GET['bike']) ? htmlspecialchars($_GET['bike']) : "Unknown Bike";
$price = isset($_GET['price']) ? floatval($_GET['price']) : 0;
$message = "";
$msg_type = "";

// Fetch current user details to pre-fill form
$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT full_name, phone FROM users WHERE id = $user_id");
$user_data = $user_query->fetch_assoc();

// Handle Form Submission
if (isset($_POST['confirm_booking'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $address = $_POST['address'];
    $license_number = strtoupper(trim($_POST['license_number']));

    // Calculate days and total price
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $days = $end->diff($start)->days + 1; // +1 to include the start day
    
    if ($days < 1) {
        $message = "End date must be after start date!";
        $msg_type = "danger";
    } elseif (strlen($license_number) < 5 || strlen($license_number) > 20 || !preg_match('/^[A-Z0-9\s-]+$/', $license_number)) {
        $message = "Invalid License Number! Must be 5-20 alphanumeric characters.";
        $msg_type = "danger";
    } else {
        $total_price = $days * $price;
        
        // Store booking details in session for the payment page
        $_SESSION['temp_booking'] = [
            'user_id' => $user_id,
            'bike_name' => $bike_name,
            'price_per_day' => $price,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_price' => $total_price,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone,
            'address' => $address,
            'license_number' => $license_number
        ];
        
        header("Location: payment.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ride | Eazy Ride</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #2ECC71; --dark-color: #2c3e50; }
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: var(--dark-color) !important; }
        .nav-link { font-weight: 500; color: var(--dark-color); }
        .nav-link:hover { color: var(--primary-color); }
        .booking-card { max-width: 600px; margin: 100px auto 60px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 15px; overflow: hidden; }
        .header-bg { background: var(--dark-color); color: white; padding: 30px; text-align: center; }
        .btn-confirm { background-color: var(--primary-color); border: none; color: white; font-weight: 600; }
        .btn-confirm:hover { background-color: #27ae60; color: white; }
        footer { background-color: #222; color: #ddd; padding: 30px 0; margin-top: auto; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-motorcycle text-success"></i> Eazy Ride</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="all_bikes.php">Full Fleet</a></li>
                    <li class="nav-item ms-lg-3">
                        <span class="fw-bold me-2">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card booking-card">
            <div class="header-bg">
                <h3 class="fw-bold"><i class="fas fa-calendar-check text-success"></i> Complete Booking</h3>
                <p class="mb-0 opacity-75">You're almost there! Finalize your ride details.</p>
            </div>
            <div class="card-body p-4">
                
                <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded">
                    <div>
                        <small class="text-muted d-block">Selected Bike</small>
                        <h5 class="mb-0 fw-bold text-dark"><?php echo $bike_name; ?></h5>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Price Rate</small>
                        <h5 class="mb-0 fw-bold text-success">₹<?php echo $price; ?> <span class="small text-muted fw-normal">/day</span></h5>
                    </div>
                </div>
                
                <?php if($message != ""): ?>
                    <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <h6 class="fw-bold mb-3">Personal Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="customer_phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    
                    <h6 class="fw-bold mb-3 mt-4">Booking Details</h6>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Pick-up Date</label>
                            <input type="date" name="start_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Return Date</label>
                            <input type="date" name="end_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Driving License Number</label>
                            <input type="text" name="license_number" class="form-control" required minlength="5" maxlength="20" pattern="[A-Z0-9\s-]+" title="5-20 alphanumeric characters" oninput="this.value = this.value.toUpperCase()">
                        </div>
                    </div>

                    <div class="alert alert-light border text-center">
                        <small class="text-muted">Total amount will be calculated upon confirmation.</small>
                    </div>

                    <button type="submit" name="confirm_booking" class="btn btn-confirm w-100 py-2 rounded-pill shadow-sm">
                        Confirm & Pay
                    </button>
                    <a href="all_bikes.php" class="btn btn-link w-100 mt-2 text-decoration-none text-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <h5 class="text-white mb-3">Eazy Ride</h5>
            <div class="d-flex justify-content-center gap-3 mb-3">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
            <small class="text-muted">&copy; 2024 Eazy Ride Rentals. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>