<?php
$pageTitle = 'Register';
$showHeader = false;
$showFooter = false;
$showLogoutModal = false;
$showDeleteModal = false;
require_once __DIR__ . '/../includes/header.php';
?>
<div class="auth-container">
	<div class="register-container">
		<div class="logo">
			<i class="fas fa-leaf"></i>
			Green Trade Admin
		</div>
		<div class="subtitle">Create Administrator Account</div>
		
		<div class="error-message" id="errorMessage"></div>
		<div class="success-message" id="successMessage"></div>
		
		<form id="registerForm">
			<div class="form-row">
				<div class="form-group">
					<label for="firstName">First Name</label>
					<input type="text" id="firstName" name="firstName" required>
				</div>
				
				<div class="form-group">
					<label for="lastName">Last Name</label>
					<input type="text" id="lastName" name="lastName" required>
				</div>
			</div>
			
			<div class="form-group">
				<label for="email">Email Address</label>
				<input type="email" id="email" name="email" required>
			</div>
			
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" id="password" name="password" required>
			</div>
			
			<div class="form-group">
				<label for="confirmPassword">Confirm Password</label>
				<input type="password" id="confirmPassword" name="confirmPassword" required>
			</div>
			
			<div class="password-requirements" id="passwordRequirements">
				<h4>Password Requirements:</h4>
				<ul>
					<li id="length">At least 8 characters</li>
					<li id="uppercase">At least one uppercase letter</li>
					<li id="lowercase">At least one lowercase letter</li>
					<li id="number">At least one number</li>
					<li id="special">At least one special character</li>
				</ul>
			</div>
			
			<button type="submit" class="register-btn">
				<span class="loading" id="loading">
					<i class="fas fa-spinner"></i> Creating Account...
				</span>
				<span class="normal" id="normalText">Create Account</span>
			</button>
		</form>
		
		<div style="margin-top: 20px;">
			<a href="login.php" class="login-link">Already have an account? Login here</a>
		</div>
	</div>
</div>

<?php
$additionalJS = '<script>
 function validatePassword(password) {
   const requirements = {
     length: password.length >= 8,
     uppercase: /[A-Z]/.test(password),
     lowercase: /[a-z]/.test(password),
     number: /[0-9]/.test(password),
     special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
   };
   Object.keys(requirements).forEach(req => {
     const el = document.getElementById(req);
     if (requirements[req]) { el.classList.add("valid"); el.classList.remove("invalid"); }
     else { el.classList.add("invalid"); el.classList.remove("valid"); }
   });
   return Object.values(requirements).every(Boolean);
 }
 document.getElementById("password").addEventListener("input", function() { validatePassword(this.value); });
 document.getElementById("registerForm").addEventListener("submit", async function(e) {
   e.preventDefault();
   const firstName = document.getElementById("firstName").value;
   const lastName = document.getElementById("lastName").value;
   const email = document.getElementById("email").value;
   const password = document.getElementById("password").value;
   const confirmPassword = document.getElementById("confirmPassword").value;
   if (password !== confirmPassword) { showMessage("errorMessage", "Passwords do not match.", true); return; }
   if (!validatePassword(password)) { showMessage("errorMessage", "Password does not meet requirements.", true); return; }
   document.getElementById("loading").style.display = "inline";
   document.getElementById("normalText").style.display = "none";
   document.getElementById("errorMessage").style.display = "none";
   try {
     const cred = await firebase.auth().createUserWithEmailAndPassword(email, password);
     const user = cred.user;
     await user.updateProfile({ displayName: `${firstName} ${lastName}` });
     await user.sendEmailVerification();
     showMessage("successMessage", "Account created successfully! Please check your email for verification.", false);
     setTimeout(() => { window.location.href = "login.php"; }, 3000);
   } catch (error) {
     let msg = "Registration failed. Please try again.";
     switch (error.code) {
       case "auth/email-already-in-use": msg = "An account with this email already exists."; break;
       case "auth/invalid-email": msg = "Invalid email address."; break;
       case "auth/weak-password": msg = "Password is too weak."; break;
     }
     showMessage("errorMessage", msg, true);
   } finally {
     document.getElementById("loading").style.display = "none";
     document.getElementById("normalText").style.display = "inline";
   }
 });
 firebase.auth().onAuthStateChanged(function(user) { if (user) { window.location.href = "../dashboard.php"; } });
</script>';
require_once __DIR__ . '/../includes/footer.php';
?>
