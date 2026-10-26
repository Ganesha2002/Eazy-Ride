<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Fleet | Eazy Ride</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2ECC71;
            --dark-color: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--dark-color) !important;
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-color);
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .card {
            border: none;
            transition: transform 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .bike-price {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.2rem;
        }

        .btn-rent {
            background-color: var(--dark-color);
            color: white;
        }

        .btn-rent:hover {
            background-color: var(--primary-color);
            color: white;
        }

        footer {
            background-color: #222;
            color: #ddd;
            padding: 30px 0;
        }
    </style>
</head>

<body>

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
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <li class="nav-item ms-lg-3">
                            <span class="fw-bold me-2">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3">
                            <a href="auth.php" class="btn btn-outline-primary rounded-pill px-4">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container" style="margin-top: 100px; margin-bottom: 60px;">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Explore Our Complete Fleet</h2>
            <div class="bg-success mx-auto" style="width: 60px; height: 3px;"></div>
            <p class="text-muted mt-3">Choose from our premium collection of 10 bikes tailored for every ride.</p>
        </div>

        <div class="row g-4">
            <?php
            // Fetch bikes from database
            $result = $conn->query("SELECT * FROM bikes");

            if ($result->num_rows > 0) {
                while ($bike = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100">
                            <img src="<?php echo $bike['image_path']; ?>" loading="lazy" class="card-img-top"
                                alt="<?php echo $bike['name']; ?>" style="height: 180px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="card-title mb-0 fw-bold"><?php echo $bike['name']; ?></h6>
                                    <span class="bike-price text-success small">₹<?php echo $bike['price']; ?>/day</span>
                                </div>
                                <p class="card-text text-muted small mb-3"><?php echo $bike['description']; ?></p>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between text-secondary small mb-3">
                                        <span><i class="fas fa-tachometer-alt"></i> <?php echo $bike['cc']; ?></span>
                                        <span><i class="fas fa-cog"></i> <?php echo $bike['type']; ?></span>
                                    </div>
                                    <a href="booking.php?bike=<?php echo urlencode($bike['name']); ?>&price=<?php echo $bike['price']; ?>"
                                        class="btn btn-rent w-100 btn-sm py-2">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                echo '<div class="col-12 text-center"><p class="text-muted">No bikes available at the moment.</p></div>';
            }
            ?>
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