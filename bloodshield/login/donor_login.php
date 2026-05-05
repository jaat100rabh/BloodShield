<!DOCTYPE html>
<html lang="en">
<head>
	<title>BloodShield Donor Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="../i/bs.jpg"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="../i/bs.jpg" alt="IMG"><h2 style="color: red"><center><b>BloodShield</b></center></h2>
					<h4 style="color: #333"><center>Donor Portal</center></h4>
				</div>

				<form action="donor_auth.php" class="login100-form validate-form" method="POST">
					<span class="login100-form-title">
						Donor Login
					</span>

					<div class="wrap-input100 validate-input" data-validate="Name is required">
						<input class="input100" type="text" name="full_name" placeholder="Full Name">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Gender is required">
						<select class="input100" name="gender" id="gender" onchange="updateWeightRequirement()">
							<option value="">Select Gender</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
							<option value="Other">Other</option>
						</select>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-venus-mars" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Valid phone is required: +91 ......">
						<input class="input100" type="text" name="phone" placeholder="Phone Number">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-phone" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Date of Birth is required">
						<input class="input100" type="date" name="date_of_birth" placeholder="Date of Birth" min="<?php echo date('Y-m-d', strtotime('-65 years')); ?>" max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-calendar" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Weight is required">
						<input class="input100" type="number" name="weight" id="weight" placeholder="Weight (kg)" min="45" max="200" step="0.1">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-weight" aria-hidden="true"></i>
						</span>
						<small id="weight_hint" style="color: #666; font-size: 12px;">Minimum weight: 45kg</small>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login as Donor
						</button>
					</div>

					<div class="text-center p-t-12">
						<a class="txt2" href="create_account.php">
							New Donor? Create Account
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>

					<div class="text-center p-t-12">
						<a class="txt2" href="login.php">
							← Back to Main Login
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	

<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})

		function updateWeightRequirement() {
			var gender = document.getElementById('gender').value;
			var weightInput = document.getElementById('weight');
			var weightHint = document.getElementById('weight_hint');
			
			if (gender === 'Male') {
				weightInput.min = '53';
				weightHint.textContent = 'Minimum weight: 53kg for males';
			} else if (gender === 'Female') {
				weightInput.min = '45';
				weightHint.textContent = 'Minimum weight: 45kg for females';
			} else {
				weightInput.min = '45';
				weightHint.textContent = 'Minimum weight: 45kg';
			}
		}
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>
