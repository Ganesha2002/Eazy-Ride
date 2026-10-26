<?php
session_start();
include 'db.php';

// Check if user is logged in AND is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth.php");
    exit();
}

// Fetch Stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='user'")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_price) as total FROM bookings")->fetch_assoc()['total'];

// Fetch All Bookings (Joined with User info)
$bookings_sql = "SELECT b.*, u.full_name, u.email, u.phone 
                 FROM bookings b 
                 JOIN users u ON b.user_id = u.id 
                 ORDER BY b.created_at DESC";
$bookings = $conn->query($bookings_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Eazy Ride</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { background: #2ECC71; color: white; }
        .stat-card { border-left: 5px solid #2ECC71; }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar col-md-2 d-none d-md-block p-3">
        <h4 class="mb-4 text-center"><i class="fas fa-motorcycle text-success"></i> Admin</h4>
        <a href="#" class="active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="index.php"><i class="fas fa-home me-2"></i> View Site</a>
        <a href="logout.php" class="mt-5 text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <div class="col-md-10 bg-light p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard Overview</h2>
            <a href="logout.php" class="btn btn-outline-danger d-md-none">Logout</a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm p-3 stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Users</h6>
                            <h3><?php echo $total_users; ?></h3>
                        </div>
                        <i class="fas fa-users fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-3 stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Bookings</h6>
                            <h3><?php echo $total_bookings; ?></h3>
                        </div>
                        <i class="fas fa-calendar-check fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-3 stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Revenue</h6>
                            <h3 class="text-success">₹<?php echo number_format($total_revenue, 2); ?></h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">All Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>User</th>
                                <th>Bike Info</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bookings->num_rows > 0): ?>
                                <?php while($row = $bookings->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td>
                                        <strong><?php echo $row['full_name']; ?></strong><br>
                                        <small class="text-muted"><?php echo $row['phone']; ?></small>
                                    </td>
                                    <td><?php echo $row['bike_name']; ?></td>
                                    <td>
                                        <small><?php echo date('M d', strtotime($row['start_date'])); ?> to <?php echo date('M d', strtotime($row['end_date'])); ?></small>
                                    </td>
                                    <td class="fw-bold">₹<?php echo $row['total_price']; ?></td>
                                    <td>
                                        <span class="badge bg-success"><?php echo $row['status']; ?></span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No bookings found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>