<?php
session_start();
include 'db.php';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Handle Login Submission
$error = "";
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check for user with admin role
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $user['full_name'];
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid Password";
        }
    } else {
        $error = "Access Denied: Admin account not found.";
    }
}

// Handle Booking Details Update (Reschedule/Cancel)
if (isset($_POST['update_booking_details']) && isset($_SESSION['admin_logged_in'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Recalculate price based on new dates
    $stmt_price = $conn->prepare("SELECT price_per_day FROM bookings WHERE id = ?");
    $stmt_price->bind_param("i", $booking_id);
    $stmt_price->execute();
    $res_price = $stmt_price->get_result();
    $row_price = $res_price->fetch_assoc();
    $price_per_day = $row_price['price_per_day'];

    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $days = $end->diff($start)->days + 1;
    $new_total = ($days > 0) ? $days * $price_per_day : 0;

    $stmt = $conn->prepare("UPDATE bookings SET status=?, start_date=?, end_date=?, total_price=? WHERE id=?");
    $stmt->bind_param("sssdi", $status, $start_date, $end_date, $new_total, $booking_id);
    $stmt->execute();
    
    header("Location: admin.php");
    exit();
}

// Handle Add New Bike
if (isset($_POST['add_bike']) && isset($_SESSION['admin_logged_in'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cc = $_POST['cc'];
    $type = $_POST['type'];
    $desc = $_POST['desc'];
    
    // Image Upload Logic
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $image_name = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO bikes (name, price, image_path, cc, type, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssss", $name, $price, $target_file, $cc, $type, $desc);
        $stmt->execute();
        header("Location: admin.php?view=fleet&msg=Bike added successfully");
        exit();
    }
}

// Handle Delete Bike
if (isset($_GET['delete_bike']) && isset($_SESSION['admin_logged_in'])) {
    $id = intval($_GET['delete_bike']);
    $conn->query("DELETE FROM bikes WHERE id = $id");
    header("Location: admin.php?view=fleet&msg=Bike deleted successfully");
    exit();
}

// Handle Edit Bike
if (isset($_POST['edit_bike']) && isset($_SESSION['admin_logged_in'])) {
    $id = $_POST['bike_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cc = $_POST['cc'];
    $type = $_POST['type'];
    $desc = $_POST['desc'];
    
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $stmt = $conn->prepare("UPDATE bikes SET name=?, price=?, image_path=?, cc=?, type=?, description=? WHERE id=?");
        $stmt->bind_param("sdssssi", $name, $price, $target_file, $cc, $type, $desc, $id);
    } else {
        $stmt = $conn->prepare("UPDATE bikes SET name=?, price=?, cc=?, type=?, description=? WHERE id=?");
        $stmt->bind_param("sdsssi", $name, $price, $cc, $type, $desc, $id);
    }
    $stmt->execute();
    header("Location: admin.php?view=fleet&msg=Bike updated successfully");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Eazy Ride</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .login-card { max-width: 400px; margin: 100px auto; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; }
        .nav-link { color: rgba(255,255,255,0.8); }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.1); }
        .status-select { min-width: 120px; }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['admin_logged_in'])): ?>
    <!-- Admin Login View -->
    <div class="container">
        <div class="card login-card border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0"><i class="fas fa-user-shield"></i> Admin Login</h4>
            </div>
            <div class="card-body p-4">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-dark w-100">Login to Dashboard</button>
                </form>
                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none text-secondary small">Back to Website</a>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Admin Dashboard View -->
    <?php $view = isset($_GET['view']) ? $_GET['view'] : 'bookings'; ?>
    
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3 d-none d-md-block" style="width: 250px;">
            <h4 class="mb-4 ps-2">Eazy Ride Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="?view=bookings" class="nav-link <?php echo $view == 'bookings' ? 'active' : ''; ?>"><i class="fas fa-list me-2"></i> Bookings</a></li>
                <li class="nav-item"><a href="?view=fleet" class="nav-link <?php echo $view == 'fleet' ? 'active' : ''; ?>"><i class="fas fa-motorcycle me-2"></i> Bike</a></li>
                <li class="nav-item"><a href="?action=logout" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            
            <?php if($view == 'bookings'): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Booking Management</h2>
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Bike</th>
                                    <th>Dates</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $bookings = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC");
                                while($row = $bookings->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['customer_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['customer_phone']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['bike_name']); ?></td>
                                    <td>
                                        <small><?php echo date('M d', strtotime($row['start_date'])); ?> - <?php echo date('M d', strtotime($row['end_date'])); ?></small>
                                    </td>
                                    <td>₹<?php echo $row['total_price']; ?></td>
                                    <td>
                                        <?php 
                                        $badgeClass = match($row['status']) {
                                            'Confirmed' => 'bg-success',
                                            'Pending' => 'bg-warning text-dark',
                                            'Cancelled' => 'bg-danger',
                                            'Completed' => 'bg-primary',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $row['status']; ?></span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editBookingModal"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-start="<?php echo $row['start_date']; ?>"
                                            data-end="<?php echo $row['end_date']; ?>"
                                            data-status="<?php echo $row['status']; ?>">
                                            <i class="fas fa-edit"></i> Manage
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php elseif($view == 'fleet'): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Bike Management</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBikeModal"><i class="fas fa-plus"></i> Add New Bike</button>
            </div>

            <div class="row g-4">
                <?php
                $bikes = $conn->query("SELECT * FROM bikes ORDER BY created_at DESC");
                while($bike = $bikes->fetch_assoc()):
                ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo $bike['image_path']; ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="fw-bold"><?php echo $bike['name']; ?></h6>
                            <p class="text-success fw-bold mb-1">₹<?php echo $bike['price']; ?>/day</p>
                            <small class="text-muted"><?php echo $bike['cc']; ?> | <?php echo $bike['type']; ?></small>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between pb-3">
                            <button class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editBikeModal"
                                data-id="<?php echo $bike['id']; ?>"
                                data-name="<?php echo htmlspecialchars($bike['name']); ?>"
                                data-price="<?php echo $bike['price']; ?>"
                                data-cc="<?php echo htmlspecialchars($bike['cc']); ?>"
                                data-type="<?php echo htmlspecialchars($bike['type']); ?>"
                                data-desc="<?php echo htmlspecialchars($bike['description']); ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <a href="admin.php?delete_bike=<?php echo $bike['id']; ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Are you sure you want to remove this bike?');">
                                <i class="fas fa-trash"></i> Remove
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
            
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div class="modal fade" id="editBookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="modal_booking_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="modal_status" class="form-select">
                                <option value="Confirmed">Confirmed</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="modal_start" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" id="modal_end" class="form-control" required>
                            </div>
                        </div>
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle"></i> Changing dates will automatically recalculate the total price.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_booking_details" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Bike Modal -->
    <div class="modal fade" id="addBikeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Bike</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Bike Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6"><label class="form-label">Price (₹/day)</label><input type="number" name="price" class="form-control" required></div>
                            <div class="col-6"><label class="form-label">Engine (CC)</label><input type="text" name="cc" class="form-control" required></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transmission Type</label>
                            <select name="type" class="form-select"><option>Manual</option><option>Auto</option></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="desc" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bike Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_bike" class="btn btn-success">Upload Bike</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Bike Modal -->
    <div class="modal fade" id="editBikeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Bike Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="bike_id" id="edit_bike_id">
                        <div class="mb-3">
                            <label class="form-label">Bike Name</label>
                            <input type="text" name="name" id="edit_bike_name" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6"><label class="form-label">Price (₹/day)</label><input type="number" name="price" id="edit_bike_price" class="form-control" required></div>
                            <div class="col-6"><label class="form-label">Engine (CC)</label><input type="text" name="cc" id="edit_bike_cc" class="form-control" required></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transmission Type</label>
                            <select name="type" id="edit_bike_type" class="form-select"><option>Manual</option><option>Auto</option></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="desc" id="edit_bike_desc" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Update Image (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="edit_bike" class="btn btn-primary">Update Bike</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const editModal = document.getElementById('editBookingModal');
        editModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            
            // Extract info from data-* attributes
            const id = button.getAttribute('data-id');
            const start = button.getAttribute('data-start');
            const end = button.getAttribute('data-end');
            const status = button.getAttribute('data-status');
            
            // Update the modal's content.
            editModal.querySelector('#modal_booking_id').value = id;
            editModal.querySelector('#modal_start').value = start;
            editModal.querySelector('#modal_end').value = end;
            editModal.querySelector('#modal_status').value = status;
        });

        const editBikeModal = document.getElementById('editBikeModal');
        editBikeModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            editBikeModal.querySelector('#edit_bike_id').value = button.getAttribute('data-id');
            editBikeModal.querySelector('#edit_bike_name').value = button.getAttribute('data-name');
            editBikeModal.querySelector('#edit_bike_price').value = button.getAttribute('data-price');
            editBikeModal.querySelector('#edit_bike_cc').value = button.getAttribute('data-cc');
            editBikeModal.querySelector('#edit_bike_type').value = button.getAttribute('data-type');
            editBikeModal.querySelector('#edit_bike_desc').value = button.getAttribute('data-desc');
        });
    </script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>