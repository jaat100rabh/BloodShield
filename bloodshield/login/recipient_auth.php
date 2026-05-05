<?php
include './con.php';
session_start();

// Get form data
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$date_of_birth = $_POST['date_of_birth'];
$password = $_POST['password'];

// Validate inputs
if (empty($full_name) || empty($phone) || empty($date_of_birth) || empty($password)) {
    $_SESSION['error'] = "All fields are required!";
    header("location: recipient_login.php");
    exit();
}

// Validate phone number format (Indian format)
if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
    $_SESSION['error'] = "Invalid phone number format!";
    header("location: recipient_login.php");
    exit();
}

// Query to check recipient credentials
$query = "SELECT r.*, u.full_name, u.email, u.password 
          FROM recipients r 
          JOIN users u ON r.user_id = u.id 
          WHERE u.phone = ? AND u.full_name = ? AND u.user_type = 'recipient'";

$stmt = $con->prepare($query);
$stmt->bind_param("ss", $phone, $full_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $row['password'])) {
        // Check date of birth (format: YYYY-MM-DD)
        $stored_dob = date('Y-m-d', strtotime($row['date_of_birth']));
        $provided_dob = date('Y-m-d', strtotime($date_of_birth));
        
        if ($stored_dob === $provided_dob) {
            // Login successful
            $_SESSION['recipient_id'] = $row['id'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['phone'] = $phone;
            $_SESSION['user_type'] = 'recipient';
            $_SESSION['blood_group'] = $row['blood_group_required'];
            
            // Update last login
            $update_query = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("i", $row['user_id']);
            $update_stmt->execute();
            
            header("location: recipient_dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Date of birth doesn't match our records!";
        }
    } else {
        $_SESSION['error'] = "Invalid password!";
    }
} else {
    $_SESSION['error'] = "No recipient found with these details!";
}

header("location: recipient_login.php");
exit();
?>
