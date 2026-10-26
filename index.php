<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eazy Ride | Premium Bike Rentals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        .navbar-nav .nav-item .nav-link.active {
            color: var(--primary-color);
        }

        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            .navbar-nav .nav-item:nth-child(4) {
                /* Adjust index based on your nav structure */
                display: none;
                /* Hide "How It Works" for admin */
            }

        <?php endif; ?>

        :root {
            --primary-color: #2ECC71;
            --dark-color: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        /* Navbar Styling */
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

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Hero Styling */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1558981403-c5f9899a28bc?ixlib=rb-1.2.1');
            background-size: cover;
            background-position: center;
            height: 90vh;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        /* Search Box */
        .search-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
        }

        /* Features & Cards */
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
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

        /* Stats */
        .stats-section {
            background-color: var(--dark-color);
            color: white;
            padding: 60px 0;
        }

        .stat-number {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: bold;
        }

        footer {
            background-color: #222;
            color: #ddd;
            padding: 50px 0 20px;
        }

        footer a {
            color: #aaa;
            text-decoration: none;
        }

        footer a:hover {
            color: var(--primary-color);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-motorcycle text-success"></i> Eazy Ride</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="all_bikes.php">bikes</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>

                    <?php if (isset($_SESSION['user_name'])): ?>
                        <li class="nav-item"><a class="nav-link" href="my_booking.php">My Bookings</a></li>
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

    <section id="home" class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">Explore the City with Eazy Ride</h1>
            <p class="lead mb-5">Rent top-quality bikes and scooters at affordable rates.</p>

            <div class="search-card text-dark mx-auto" style="max-width: 900px;">
                <form class="row g-3 align-items-end" action="all_bikes.php" method="GET">
                    <div class="col-md-3 text-start">
                        <label class="form-label fw-bold">Location</label>
                        <select class="form-select" name="location" required>
                            <option value="" selected disabled>Select Location</option>
                            <option value="New York">New York</option>
                            <option value="London">London</option>
                            <option value="Paris">Paris</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-start">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <div class="col-md-3 text-start">
                        <label class="form-label fw-bold">Type</label>
                        <select class="form-select" name="type" required>
                            <option value="" selected disabled>Select Type</option>
                            <option value="Any Type">Any Type</option>
                            <option value="Scooter">Scooter</option>
                            <option value="Sport">Sport</option>
                            <option value="Cruiser">Cruiser</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Find Bike</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">How It Works</h2>
                <div class="bg-success mx-auto" style="width: 60px; height: 3px;"></div>
            </div>
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="p-3">
                        <i class="fas fa-search-location feature-icon"></i>
                        <h4>1. Choose Location</h4>
                        <p class="text-muted">Select your pick-up point and dates.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <i class="fas fa-motorcycle feature-icon"></i>
                        <h4>2. Select Bike</h4>
                        <p class="text-muted">Browse our wide range of bikes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <i class="fas fa-road feature-icon"></i>
                        <h4>3. Enjoy the Ride</h4>
                        <p class="text-muted">Pay securely and zoom away!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fleet" class="py-5 bg-light">

        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Our Top Rentals</h2>
                <div class="bg-success mx-auto" style="width: 60px; height: 3px;"></div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="https://images.unsplash.com/photo-1505159940484-eb2b9f2588e2?auto=format&fit=crop&w=600&q=80"
                            loading="lazy" class="card-img-top" alt="Scooter" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Urban Scooter</h5>
                                <span class="bike-price">₹500/day</span>
                            </div>
                            <p class="card-text text-muted small">Perfect for city commuting.</p>
                            <div class="d-flex justify-content-between text-secondary small mb-3">
                                <span><i class="fas fa-tachometer-alt"></i> 110cc</span>
                                <span><i class="fas fa-gas-pump"></i> Petrol</span>
                                <span><i class="fas fa-cog"></i> Auto</span>
                            </div>
                            <a href="booking.php?bike=Urban Scooter&price=500" class="btn btn-rent w-100">Book Now</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="https://images.unsplash.com/photo-1558981285-6f0c94958bb6?auto=format&fit=crop&w=600&q=80"
                            loading="lazy" class="card-img-top" alt="Cruiser" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Harley Street</h5>
                                <span class="bike-price">₹1500/day</span>
                            </div>
                            <p class="card-text text-muted small">A classic cruiser for long drives.</p>
                            <div class="d-flex justify-content-between text-secondary small mb-3">
                                <span><i class="fas fa-tachometer-alt"></i> 750cc</span>
                                <span><i class="fas fa-gas-pump"></i> Petrol</span>
                                <span><i class="fas fa-cog"></i> Manual</span>
                            </div>
                            <a href="booking.php?bike=Harley Street&price=1500" class="btn btn-rent w-100">Book Now</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=600&q=80"
                            loading="lazy" class="card-img-top" alt="Sport" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Ninja Sport</h5>
                                <span class="bike-price">₹2000/day</span>
                            </div>
                            <p class="card-text text-muted small">High performance bike for thrills.</p>
                            <div class="d-flex justify-content-between text-secondary small mb-3">
                                <span><i class="fas fa-tachometer-alt"></i> 650cc</span>
                                <span><i class="fas fa-gas-pump"></i> Petrol</span>
                                <span><i class="fas fa-cog"></i> Manual</span>
                            </div>
                            <a href="booking.php?bike=Ninja Sport&price=2000" class="btn btn-rent w-100">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="stat-number">50+</div>
                    <div class="text-uppercase tracking-wider">Bike Models</div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="stat-number">10k+</div>
                    <div class="text-uppercase tracking-wider">Happy Customers</div>
                </div>
                <div class="col-md-4">
                    <div class="stat-number">24/7</div>
                    <div class="text-uppercase tracking-wider">Roadside Support</div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="text-white mb-3">Eazy Ride</h5>
                    <p class="small text-muted">The easiest way to rent a bike. Affordable prices, well-maintained
                        fleet, and instant booking availability.</p>
                    <div class="d-flex gap-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#fleet">Our Fleet</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white mb-3">Contact Info</h5>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Main Street, City Center</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +1 234 567 890</li>
                        <li><i class="fas fa-envelope me-2"></i> support@eazyride.com</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="text-center small text-muted">
                &copy; 2024 Eazy Ride Rentals. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>