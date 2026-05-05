<?php
include './con.php';
session_start();

// Get form data
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$date_of_birth = $_POST['date_of_birth'];
$gender = $_POST['gender'];
$weight = $_POST['weight'];
$password = $_POST['password'];

// Validate inputs
if (empty($full_name) || empty($phone) || empty($date_of_birth) || empty($gender) || empty($weight) || empty($password)) {
    $_SESSION['error'] = "All fields are required!";
    header("location: donor_login.php");
    exit();
}

// Validate gender
if (!in_array($gender, ['Male', 'Female', 'Other'])) {
    $_SESSION['error'] = "Invalid gender selection!";
    header("location: donor_login.php");
    exit();
}

// Validate weight based on gender
if ($gender === 'Male') {
    if ($weight < 53 || $weight > 200) {
        $_SESSION['error'] = "Male donors must weigh between 53kg and 200kg! Your weight: {$weight}kg";
        header("location: donor_login.php");
        exit();
    }
} elseif ($gender === 'Female') {
    if ($weight < 45 || $weight > 200) {
        $_SESSION['error'] = "Female donors must weigh between 45kg and 200kg! Your weight: {$weight}kg";
        header("location: donor_login.php");
        exit();
    }
} else {
    if ($weight < 45 || $weight > 200) {
        $_SESSION['error'] = "Donors must weigh between 45kg and 200kg! Your weight: {$weight}kg";
        header("location: donor_login.php");
        exit();
    }
}

// Validate phone number format (Indian format)
if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
    $_SESSION['error'] = "Invalid phone number format!";
    header("location: donor_login.php");
    exit();
}

// AGE VALIDATION - Must be between 18 and 65 years old
$birth_date = new DateTime($date_of_birth);
$today = new DateTime();
$age = $today->diff($birth_date)->y;

if ($age < 18 || $age > 65) {
    $_SESSION['error'] = "Donors must be between 18 and 65 years old! Your age: $age years";
    header("location: donor_login.php");
    exit();
}

// Query to check donor credentials
$query = "SELECT d.*, u.full_name, u.email, u.password 
          FROM donors d 
          JOIN users u ON d.user_id = u.id 
          WHERE u.phone = ? AND u.full_name = ? AND u.user_type = 'donor'";

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
            $_SESSION['donor_id'] = $row['id'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['phone'] = $phone;
            $_SESSION['user_type'] = 'donor';
            $_SESSION['blood_group'] = $row['blood_group'];
            $_SESSION['age'] = $age;
            
            // Update last login
            $update_query = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("i", $row['user_id']);
            $update_stmt->execute();
            
            header("location: donor_dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Date of birth doesn't match our records!";
        }
    } else {
        $_SESSION['error'] = "Invalid password!";
    }
} else {
    $_SESSION['error'] = "No donor found with these details!";
}

header("location: donor_login.php");
exit();
?>
