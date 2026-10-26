<?php
session_start();
include 'db.php';

// Redirect if no booking session exists
if (!isset($_SESSION['user_id']) || !isset($_SESSION['temp_booking'])) {
    header("Location: index.php");
    exit();
}

$booking = $_SESSION['temp_booking'];
$error = "";

// Handle Payment Submission
if (isset($_POST['pay_now'])) {
    // Simulate Payment Processing...
    // If successful, insert into database
    
    $user_id = $booking['user_id'];
    $bike_name = $booking['bike_name'];
    $price = $booking['price_per_day'];
    $start_date = $booking['start_date'];
    $end_date = $booking['end_date'];
    $total_price = $booking['total_price'];
    $customer_name = $booking['customer_name'];
    $customer_phone = $booking['customer_phone'];
    $address = $booking['address'];
    $license_number = $booking['license_number'];

    // Check if user exists to prevent Foreign Key error
    $check_user = $conn->query("SELECT id FROM users WHERE id = " . intval($user_id));
    if ($check_user->num_rows == 0) {
        session_destroy();
        header("Location: auth.php?message=Session expired. Please login again.");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, bike_name, price_per_day, start_date, end_date, total_price, customer_name, customer_phone, address, license_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdssdssss", $user_id, $bike_name, $price, $start_date, $end_date, $total_price, $customer_name, $customer_phone, $address, $license_number);

    if ($stmt->execute()) {
        // Clear temp session
        unset($_SESSION['temp_booking']);
        // Redirect to My Bookings with success flag
        header("Location: my_booking.php?payment_success=1");
        exit();
    } else {
        $error = "Transaction Failed: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment | Eazy Ride</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .payment-card { max-width: 500px; margin: 80px auto; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .card-header { background: #2c3e50; color: white; border-radius: 15px 15px 0 0 !important; padding: 20px; }
        .total-amount { font-size: 2rem; font-weight: 700; color: #2ECC71; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card payment-card">
            <div class="card-header text-center">
                <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i> Secure Payment</h4>
            </div>
            <div class="card-body p-4">
                
                <div class="text-center mb-4">
                    <p class="text-muted mb-0">Total Amount to Pay</p>
                    <div class="total-amount">₹<?php echo number_format($booking['total_price'], 2); ?></div>
                    <small class="text-muted">For <?php echo $booking['bike_name']; ?></small>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Cardholder Name</label>
                        <input type="text" class="form-control" placeholder="John Doe" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Card Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                            <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Expiry</label>
                                <input type="text" class="form-control" placeholder="MM/YY" maxlength="5" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">CVV</label>
                                <input type="password" class="form-control" placeholder="123" maxlength="3" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="pay_now" class="btn btn-success w-100 py-2 fw-bold mt-3">
                        Pay ₹<?php echo number_format($booking['total_price'], 2); ?>
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="booking.php?bike=<?php echo urlencode($booking['bike_name']); ?>&price=<?php echo $booking['price_per_day']; ?>" class="text-decoration-none text-secondary small d-block mb-2">Cancel Payment</a>
                        <a href="index.php" class="text-decoration-none text-primary small"><i class="fas fa-home"></i> Back to Home</a>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

</body>
</html>