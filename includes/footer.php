	<?php
	// Use same prefix logic as header
	$scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
	$depth = $scriptDir === '' ? 0 : count(array_filter(explode('/', $scriptDir)));
	$assetPrefix = str_repeat('../', $depth);
	?>
	<?php if (isset($showFooter) && $showFooter): ?>
	<div class="footer">
		<p>&copy; 2024 Green Trade Admin. All rights reserved.</p>
	</div>
	<?php endif; ?>

	<?php if (isset($showLogoutModal) && $showLogoutModal): ?>
	<div id="logoutModal" class="modal">
		<div class="modal-content">
			<div class="modal-header">
				<h3>Confirm Logout</h3>
				<span class="close" onclick="closeLogoutModal()">&times;</span>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to logout?</p>
			</div>
			<div class="modal-footer">
				<button class="btn-secondary" onclick="closeLogoutModal()">Cancel</button>
				<button class="btn-primary" onclick="logout()">Logout</button>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if (isset($showDeleteModal) && $showDeleteModal): ?>
	<div id="deleteModal" class="modal">
		<div class="modal-content">
			<div class="modal-header">
				<h3>Confirm Delete</h3>
				<span class="close" onclick="closeDeleteModal()">&times;</span>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this item? This action cannot be undone.</p>
			</div>
			<div class="modal-footer">
				<button class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
				<button class="btn-danger" onclick="confirmDelete()">Delete</button>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Firebase Scripts -->
	<script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-app-compat.js"></script>
	<script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-auth-compat.js"></script>
	<script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-firestore-compat.js"></script>
	
	<!-- Firebase Configuration -->
	<script>
		const firebaseConfig = <?php echo getFirebaseConfigJS(); ?>;
		if (!firebase.apps.length) {
			firebase.initializeApp(firebaseConfig);
		}
	</script>
	
	<!-- Common JavaScript -->
	<script src="<?php echo $assetPrefix; ?>assets/js/common.js"></script>
	
	<?php if (isset($additionalJS)) echo $additionalJS; ?>
</body>
</html>
