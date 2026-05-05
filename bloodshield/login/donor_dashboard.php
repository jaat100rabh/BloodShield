<?php
session_start();

// Check if user is logged in as donor
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'donor') {
    header("location: donor_login.php");
    exit();
}

include './con.php';

// Get donor details
$donor_id = $_SESSION['donor_id'];
$query = "SELECT d.*, u.full_name, u.email, u.phone 
          FROM donors d 
          JOIN users u ON d.user_id = u.id 
          WHERE d.id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();

// Calculate age
$birth_date = new DateTime($donor['date_of_birth']);
$today = new DateTime();
$age = $today->diff($birth_date)->y;

// Get donation history
$donations_query = "SELECT d.*, br.blood_group, br.hospital_name 
                    FROM donations d 
                    JOIN blood_requests br ON d.request_id = br.id 
                    WHERE d.donor_id = ? 
                    ORDER BY d.donation_date DESC 
                    LIMIT 5";
$don_stmt = $con->prepare($donations_query);
$don_stmt->bind_param("i", $donor_id);
$don_stmt->execute();
$donations_result = $don_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>BloodShield - Donor Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../i/bs.jpg"/>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #00b894 0%, #00a885 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .btn-donor {
            background: #00b894;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
        }
        .btn-donor:hover {
            background: #00a885;
        }
        .availability-badge {
            font-size: 14px;
            padding: 5px 15px;
            border-radius: 20px;
        }
        .available {
            background: #00b894;
            color: white;
        }
        .not-available {
            background: #ff4757;
            color: white;
        }
    </style>
</head>
<body style="background: #f5f5f5;">
    <div class="container">
        <div class="dashboard-header">
            <div class="row">
                <div class="col-md-8">
                    <h2>Welcome, <?php echo htmlspecialchars($donor['full_name']); ?>!</h2>
                    <p>Blood Group: <strong><?php echo htmlspecialchars($donor['blood_group']); ?></strong></p>
                    <p>Age: <?php echo $age; ?> years | Phone: <?php echo htmlspecialchars($donor['phone']); ?></p>
                    <p>Location: <?php echo htmlspecialchars($donor['city']); ?></p>
                    <p>Status: <span class="availability-badge <?php echo $donor['is_available'] ? 'available' : 'not-available'; ?>">
                        <?php echo $donor['is_available'] ? 'Available to Donate' : 'Not Available'; ?>
                    </span></p>
                </div>
                <div class="col-md-4 text-right">
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fa fa-tint" style="font-size: 48px; color: #00b894;"></i>
                    <h3>Total Donations</h3>
                    <h2><?php echo htmlspecialchars($donor['total_donations']); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fa fa-calendar" style="font-size: 48px; color: #0984e3;"></i>
                    <h3>Last Donation</h3>
                    <h2><?php echo $donor['last_donation_date'] ? date('d M Y', strtotime($donor['last_donation_date'])) : 'Never'; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fa fa-heartbeat" style="font-size: 48px; color: #ff4757;"></i>
                    <h3>Lives Saved</h3>
                    <h2><?php echo htmlspecialchars($donor['total_donations'] * 3); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fa fa-weight" style="font-size: 48px; color: #6c5ce7;"></i>
                    <h3>Weight</h3>
                    <h2><?php echo htmlspecialchars($donor['weight']); ?> kg</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="stat-card">
                    <h3>Quick Actions</h3>
                    <div class="text-center">
                        <a href="find_requests.php" class="btn-donor">
                            <i class="fa fa-search"></i> Find Blood Requests
                        </a>
                        <a href="update_profile.php" class="btn btn-info" style="margin-left: 10px;">
                            <i class="fa fa-edit"></i> Update Profile
                        </a>
                        <a href="donation_history.php" class="btn btn-secondary" style="margin-left: 10px;">
                            <i class="fa fa-history"></i> Donation History
                        </a>
                        <?php if ($donor['is_available']): ?>
                            <a href="toggle_availability.php?status=0" class="btn btn-warning" style="margin-left: 10px;">
                                <i class="fa fa-pause"></i> Set Not Available
                            </a>
                        <?php else: ?>
                            <a href="toggle_availability.php?status=1" class="btn btn-success" style="margin-left: 10px;">
                                <i class="fa fa-play"></i> Set Available
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="stat-card">
                    <h3>Recent Donations</h3>
                    <?php if ($donations_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Blood Group</th>
                                        <th>Hospital</th>
                                        <th>Units</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($donation = $donations_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('d M Y', strtotime($donation['donation_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($donation['blood_group']); ?></td>
                                            <td><?php echo htmlspecialchars($donation['hospital_name']); ?></td>
                                            <td><?php echo htmlspecialchars($donation['units_donated']); ?></td>
                                            <td><span class="badge badge-<?php echo $donation['status'] == 'Completed' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($donation['status']); ?></span></td>
                                            <td>
                                                <a href="view_donation.php?id=<?php echo $donation['id']; ?>" class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No donations found. <a href="find_requests.php">Find blood requests to donate</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="stat-card">
                    <h3>Health Information</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Weight:</strong> <?php echo htmlspecialchars($donor['weight']); ?> kg</p>
                            <p><strong>Gender:</strong> <?php echo htmlspecialchars($donor['gender']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Medical Conditions:</strong> <?php echo $donor['medical_conditions'] ? htmlspecialchars($donor['medical_conditions']) : 'None'; ?></p>
                            <p><strong>Next Eligible Date:</strong> <?php echo $donor['last_donation_date'] ? date('d M Y', strtotime($donor['last_donation_date'] . ' +90 days')) : 'Immediately'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
