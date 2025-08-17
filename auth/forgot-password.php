<?php
$pageTitle = 'Forgot Password';
$showHeader = false;
$showFooter = false;
$showLogoutModal = false;
$showDeleteModal = false;
require_once __DIR__ . '/../includes/header.php';
?>
<div class="auth-container">
	<div class="forgot-container">
		<div class="logo">
			<i class="fas fa-leaf"></i>
			Green Trade Admin
		</div>
		<div class="subtitle">Password Recovery</div>

		<div class="email-icon">
			<i class="fas fa-envelope"></i>
		</div>

		<div class="description">
			Enter your email address and we'll send you a link to reset your password.
		</div>

		<div class="error-message" id="errorMessage"></div>
		<div class="success-message" id="successMessage"></div>

		<form id="forgotForm">
			<div class="form-group">
				<label for="email">Email Address</label>
				<input type="email" id="email" name="email" required>
			</div>

			<button type="submit" class="reset-btn">
				<span class="loading" id="loading">
					<i class="fas fa-spinner"></i> Sending...
				</span>
				<span class="normal" id="normalText">Send Reset Link</span>
			</button>
		</form>

		<div style="margin-top: 20px;">
			<a href="login.php" class="back-link">Back to Login</a>
		</div>
	</div>
</div>

<?php
$additionalJS = '<script>
 document.getElementById("forgotForm").addEventListener("submit", async function(e) {
   e.preventDefault();
   const email = document.getElementById("email").value;
   document.getElementById("loading").style.display = "inline";
   document.getElementById("normalText").style.display = "none";
   document.getElementById("errorMessage").style.display = "none";
   document.getElementById("successMessage").style.display = "none";
   try {
     await firebase.auth().sendPasswordResetEmail(email);
     showMessage("successMessage", "Password reset link sent! Please check your email.", false);
     document.getElementById("email").value = "";
   } catch (error) {
     let msg = "Failed to send reset link. Please try again.";
     switch (error.code) {
       case "auth/user-not-found": msg = "No account found with this email address."; break;
       case "auth/invalid-email": msg = "Invalid email address."; break;
       case "auth/too-many-requests": msg = "Too many requests. Please try again later."; break;
     }
     showMessage("errorMessage", msg, true);
   } finally {
     document.getElementById("loading").style.display = "none";
     document.getElementById("normalText").style.display = "inline";
   }
 });
 
 firebase.auth().onAuthStateChanged(function(user) {
   if (user) {
     window.location.href = "../dashboard.php";
   }
 });
</script>';
require_once __DIR__ . '/../includes/footer.php';
?>