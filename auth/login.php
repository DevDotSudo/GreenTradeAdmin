<?php
$pageTitle = 'Login';
$showHeader = false;
$showFooter = false;
$showLogoutModal = false;
$showDeleteModal = false;
require_once __DIR__ . '/../includes/header.php';
?>
<div class="auth-container">
	<div class="login-container">
		<div class="logo">
			<i class="fas fa-leaf"></i>
			Green Trade Admin
		</div>
		<div class="subtitle">Administrator Access Portal</div>

		<div class="error-message" id="errorMessage"></div>
		<div class="success-message" id="successMessage"></div>

		<form id="loginForm">
			<div class="form-group">
				<label for="email">Email Address</label>
				<input type="email" id="email" name="email" required>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" id="password" name="password" required>
			</div>

			<button type="submit" class="login-btn">
				<span class="loading" id="loading">
					<i class="fas fa-spinner"></i> Logging in...
				</span>
				<span class="normal" id="normalText">Login</span>
			</button>
		</form>

		<div style="margin-top: 20px;">
			<a href="register.php" class="register-link">Don't have an account? Register here</a>
		</div>

		<div style="margin-top: 15px;">
			<a href="forgot-password.php" class="register-link">Forgot Password?</a>
		</div>
	</div>
</div>

<?php
$additionalJS = '<script>
// Login form handling
 document.getElementById("loginForm").addEventListener("submit", async function(e) {
   e.preventDefault();
   const email = document.getElementById("email").value;
   const password = document.getElementById("password").value;
   document.getElementById("loading").style.display = "inline";
   document.getElementById("normalText").style.display = "none";
   document.getElementById("errorMessage").style.display = "none";
   try {
     const userCredential = await firebase.auth().signInWithEmailAndPassword(email, password);
     window.location.href = "../dashboard.php";
   } catch (error) {
     let msg = "Login failed. Please try again.";
     switch (error.code) {
       case "auth/user-not-found": msg = "No account found with this email address."; break;
       case "auth/wrong-password": msg = "Incorrect password."; break;
       case "auth/invalid-email": msg = "Invalid email address."; break;
       case "auth/user-disabled": msg = "This account has been disabled."; break;
     }
     showMessage("errorMessage", msg, true);
   } finally {
     document.getElementById("loading").style.display = "none";
     document.getElementById("normalText").style.display = "inline";
   }
 });
 
 // Redirect if already logged in
 firebase.auth().onAuthStateChanged(function(user) {
   if (user) {
     window.location.href = "../dashboard.php";
   }
 });
</script>';
require_once __DIR__ . '/../includes/footer.php';
?>