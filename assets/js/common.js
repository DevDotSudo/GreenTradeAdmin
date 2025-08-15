// Common JavaScript functions for Green Trade Admin

// Authentication state management
firebase.auth().onAuthStateChanged(function(user) {
	const path = window.location.pathname.replace(/\\/g, '/');
	const inAuthFolder = /\/auth\//.test(path);
	const indexPath = inAuthFolder ? '../index.php' : 'index.php';
	const isAuthPage = path.includes('login.php') || path.endsWith('/index.php') || path.includes('register.php') || path.includes('forgot-password.php');
	if (!user && !isAuthPage) {
		window.location.href = indexPath;
	}
});

// Modal functions
function showLogoutModal() {
	document.getElementById('logoutModal')?.style.setProperty('display', 'block');
}

function closeLogoutModal() {
	document.getElementById('logoutModal')?.style.setProperty('display', 'none');
}

function showDeleteModal() {
	document.getElementById('deleteModal')?.style.setProperty('display', 'block');
}

function closeDeleteModal() {
	document.getElementById('deleteModal')?.style.setProperty('display', 'none');
}

// Logout function
function logout() {
	const path = window.location.pathname.replace(/\\/g, '/');
	const inAuthFolder = /\/auth\//.test(path);
	const indexPath = inAuthFolder ? '../index.php' : 'index.php';
	firebase.auth().signOut().then(function() {
		window.location.href = indexPath;
	}).catch(function(error) {
		console.error('Logout error:', error);
	});
}

// Utility functions
function showMessage(elementId, message, isError = false) {
	const element = document.getElementById(elementId);
	if (element) {
		element.textContent = message;
		element.style.display = 'block';
		
		if (isError) {
			element.style.background = '#ffebee';
			element.style.color = '#c62828';
		} else {
			element.style.background = '#e8f5e9';
			element.style.color = '#2e7d32';
		}
		
		setTimeout(() => {
			element.style.display = 'none';
		}, 5000);
	}
}

function hideMessage(elementId) {
	const element = document.getElementById(elementId);
	if (element) {
		element.style.display = 'none';
	}
}

// Search and filter functions
function filterTable() {
	const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
	const statusFilter = document.getElementById('statusFilter')?.value || '';
	const categoryFilter = document.getElementById('categoryFilter')?.value || '';
	const locationFilter = document.getElementById('locationFilter')?.value || '';
	const dateFilter = document.getElementById('dateFilter')?.value || '';
	
	const table = document.querySelector('.data-table');
	if (!table) return;
	
	const rows = table.querySelectorAll('tbody tr');
	
	rows.forEach(row => {
		const text = row.textContent.toLowerCase();
		const status = row.querySelector('.status-badge')?.textContent || '';
		const category = row.querySelector('td:nth-child(3)')?.textContent || '';
		const location = row.querySelector('td:nth-child(4)')?.textContent || '';
		const date = row.querySelector('td:nth-child(5)')?.textContent || '';
		
		let show = true;
		
		// Search filter
		if (searchTerm && !text.includes(searchTerm)) {
			show = false;
		}
		
		// Status filter
		if (statusFilter && status !== statusFilter) {
			show = false;
		}
		
		// Category filter
		if (categoryFilter && category !== categoryFilter) {
			show = false;
		}
		
		// Location filter
		if (locationFilter && location !== locationFilter) {
			show = false;
		}
		
		// Date filter
		if (dateFilter && !checkDateFilter(date, dateFilter)) {
			show = false;
		}
		
		row.style.display = show ? '' : 'none';
	});
}

function checkDateFilter(orderDate, filter) {
	if (!filter) return true;
	
	const today = new Date();
	const orderDateObj = new Date(orderDate);
	
	switch (filter) {
		case 'today':
			return orderDateObj.toDateString() === today.toDateString();
		case 'week':
			const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
			return orderDateObj >= weekAgo;
		case 'month':
			const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
			return orderDateObj >= monthAgo;
		default:
			return true;
	}
}

// Close modals when clicking outside
window.onclick = function(event) {
	const logoutModal = document.getElementById('logoutModal');
	const deleteModal = document.getElementById('deleteModal');
	
	if (event.target === logoutModal) {
		closeLogoutModal();
	}
	
	if (event.target === deleteModal) {
		closeDeleteModal();
	}
}

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
	if (event.key === 'Escape') {
		closeLogoutModal();
		closeDeleteModal();
	}
});

// Alert function for showing success/error messages
function showAlert(message, type = 'info') {
	// Create alert element
	const alertDiv = document.createElement('div');
	alertDiv.className = `alert alert-${type}`;
	alertDiv.style.cssText = `
		position: fixed;
		top: 20px;
		right: 20px;
		padding: 15px 20px;
		border-radius: 6px;
		color: white;
		font-weight: 500;
		z-index: 10000;
		max-width: 400px;
		box-shadow: 0 4px 12px rgba(0,0,0,0.15);
		transform: translateX(100%);
		transition: transform 0.3s ease;
	`;
	
	// Set background color based on type
	switch (type) {
		case 'success':
			alertDiv.style.backgroundColor = '#2E7D32';
			break;
		case 'error':
			alertDiv.style.backgroundColor = '#f44336';
			break;
		case 'warning':
			alertDiv.style.backgroundColor = '#ff9800';
			break;
		default:
			alertDiv.style.backgroundColor = '#2196F3';
	}
	
	alertDiv.textContent = message;
	
	// Add to page
	document.body.appendChild(alertDiv);
	
	// Animate in
	setTimeout(() => {
		alertDiv.style.transform = 'translateX(0)';
	}, 100);
	
	// Auto remove after 5 seconds
	setTimeout(() => {
		alertDiv.style.transform = 'translateX(100%)';
		setTimeout(() => {
			if (alertDiv.parentNode) {
				alertDiv.parentNode.removeChild(alertDiv);
			}
		}, 300);
	}, 5000);
	
	// Allow manual close
	alertDiv.addEventListener('click', () => {
		alertDiv.style.transform = 'translateX(100%)';
		setTimeout(() => {
			if (alertDiv.parentNode) {
				alertDiv.parentNode.removeChild(alertDiv);
			}
		}, 300);
	});
}
