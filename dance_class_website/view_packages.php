<?php
session_start();
require 'db_connect.php'; // Ensure database connection

// Fetch all packages from the database
$sql = "SELECT * FROM packages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Packages - Dance Class</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="navbar">
        <div>Dance Class</div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>


       <!-- Offers Section -->
    <section class="offers">
        <h2>Our Packages</h2>
        <div class="package-container">
            <?php
            $result = $conn->query("SELECT * FROM packages LIMIT 3");
            while ($row = $result->fetch_assoc()):
            ?>
                <div class="package">
                    <img src="images/<?php echo $row['file_name']; ?>" alt="<?php echo $row['title']; ?>" />
                    <h3><?php echo $row['name']; ?></h3>
                    <p><?php echo $row['description']; ?></p>
                    <p><strong>Price: $<?php echo $row['price']; ?> / month</strong></p>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Dance Class. All Rights Reserved.</p>
    </footer>
</body>
</html>