<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM bookings WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | Eazy Ride</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Eazy Ride</a>
            <div class="ms-auto">
                <span class="text-white me-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Booking History</h2>
            <a href="index.php" class="btn btn-dark"><i class="fas fa-home me-2"></i>Back to Home</a>
        </div>
        
        <?php if(isset($_GET['payment_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Payment Successful!</strong> Your bike has been booked successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Bike</th>
                                    <th>Customer Details</th>
                                    <th>Dates</th>
                                    <th>Duration</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th>Booked On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): 
                                    $start = new DateTime($row['start_date']);
                                    $end = new DateTime($row['end_date']);
                                    $days = $end->diff($start)->days + 1;
                                ?>
                                <tr>
                                    <td class="fw-bold text-success"><?php echo $row['bike_name']; ?></td>
                                    <td>
                                        <small class="d-block"><strong>Name:</strong> <?php echo htmlspecialchars($row['customer_name']); ?></small>
                                        <small class="d-block"><strong>Phone:</strong> <?php echo htmlspecialchars($row['customer_phone']); ?></small>
                                        <small class="d-block text-muted">Lic: <?php echo htmlspecialchars($row['license_number']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('M d', strtotime($row['start_date'])); ?> - 
                                        <?php echo date('M d', strtotime($row['end_date'])); ?>
                                    </td>
                                    <td><?php echo $days; ?> Days</td>
                                    <td class="fw-bold">₹<?php echo $row['total_price']; ?></td>
                                    <td><span class="badge bg-success"><?php echo $row['status']; ?></span></td>
                                    <td class="text-muted small"><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted fs-5">You haven't booked any rides yet.</p>
                        <a href="index.php" class="btn btn-success">Book a Ride Now</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>                    
