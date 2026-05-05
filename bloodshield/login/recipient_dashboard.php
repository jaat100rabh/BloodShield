<?php
session_start();

// Check if user is logged in as recipient
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'recipient') {
    header("location: recipient_login.php");
    exit();
}

include './con.php';

// Get recipient details
$recipient_id = $_SESSION['recipient_id'];
$query = "SELECT r.*, u.full_name, u.email, u.phone 
          FROM recipients r 
          JOIN users u ON r.user_id = u.id 
          WHERE r.id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $recipient_id);
$stmt->execute();
$result = $stmt->get_result();
$recipient = $result->fetch_assoc();

// Get recent blood requests
$requests_query = "SELECT * FROM blood_requests 
                   WHERE recipient_id = ? 
                   ORDER BY request_date DESC 
                   LIMIT 5";
$req_stmt = $con->prepare($requests_query);
$req_stmt->bind_param("i", $recipient_id);
$req_stmt->execute();
$requests_result = $req_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>BloodShield - Recipient Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../i/bs.jpg"/>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .btn-request {
            background: #ff4757;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
        }
        .btn-request:hover {
            background: #ff3838;
        }
    </style>
</head>
<body style="background: #f5f5f5;">
    <div class="container">
        <div class="dashboard-header">
            <div class="row">
                <div class="col-md-8">
                    <h2>Welcome, <?php echo htmlspecialchars($recipient['full_name']); ?>!</h2>
                    <p>Blood Group Required: <strong><?php echo htmlspecialchars($recipient['blood_group_required']); ?></strong></p>
                    <p>Phone: <?php echo htmlspecialchars($recipient['phone']); ?></p>
                </div>
                <div class="col-md-4 text-right">
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fa fa-tint" style="font-size: 48px; color: #ff4757;"></i>
                    <h3>Blood Requests</h3>
                    <h2><?php echo $requests_result->num_rows; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fa fa-users" style="font-size: 48px; color: #00b894;"></i>
                    <h3>Available Donors</h3>
                    <h2>50+</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fa fa-map-marker" style="font-size: 48px; color: #0984e3;"></i>
                    <h3>Your Location</h3>
                    <h2><?php echo htmlspecialchars($recipient['city']); ?></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="stat-card">
                    <h3>Quick Actions</h3>
                    <div class="text-center">
                        <a href="create_blood_request.php" class="btn-request">
                            <i class="fa fa-plus"></i> Request Blood Now
                        </a>
                        <a href="find_donors.php" class="btn btn-info" style="margin-left: 10px;">
                            <i class="fa fa-search"></i> Find Donors
                        </a>
                        <a href="my_requests.php" class="btn btn-secondary" style="margin-left: 10px;">
                            <i class="fa fa-history"></i> My Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="stat-card">
                    <h3>Recent Blood Requests</h3>
                    <?php if ($requests_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Blood Group</th>
                                        <th>Units</th>
                                        <th>Urgency</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($request = $requests_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('d M Y', strtotime($request['request_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($request['blood_group']); ?></td>
                                            <td><?php echo htmlspecialchars($request['units_needed']); ?></td>
                                            <td><span class="badge badge-<?php echo $request['urgency_level'] == 'Critical' ? 'danger' : 'warning'; ?>"><?php echo htmlspecialchars($request['urgency_level']); ?></span></td>
                                            <td><span class="badge badge-<?php echo $request['status'] == 'Active' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($request['status']); ?></span></td>
                                            <td>
                                                <a href="view_request.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No blood requests found. <a href="create_blood_request.php">Create your first request</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
