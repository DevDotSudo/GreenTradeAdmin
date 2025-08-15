<?php
$pageTitle = 'Dashboard';
$showHeader = true;
$showFooter = true;
$showLogoutModal = true;
$showDeleteModal = false;
require_once __DIR__ . '/includes/header.php';
?>
<div class="main-content">
	<div class="welcome-section">
		<h2>Welcome, Admin!</h2>
		<p>This is your admin dashboard where you can manage sellers, products, and monitor orders.</p>
	</div>

	<div class="stats-grid">
		<div class="stat-card">
			<i class="fas fa-users"></i>
			<h3 id="totalSellers">-</h3>
			<p>Total Sellers</p>
		</div>
		<div class="stat-card">
			<i class="fas fa-box"></i>
			<h3 id="totalProducts">-</h3>
			<p>Total Products</p>
		</div>
		<div class="stat-card">
			<i class="fas fa-shopping-cart"></i>
			<h3 id="pendingOrders">-</h3>
			<p>Pending Orders</p>
		</div>
		<div class="stat-card">
			<i class="fas fa-coins"></i>
			<h3 id="totalSales">₱0.00</h3>
			<p>Total Sales</p>
		</div>
	</div>

	<div class="recent-section">
		<h3>Recent Products</h3>
		<div id="loadingRecent" class="loading">Loading recent products...</div>
		<table class="data-table" id="recentTable" style="display: none;">
			<thead>
				<tr>
					<th>Product</th>
					<th>Category</th>
					<th>Description</th>
					<th>Created Date</th>
				</tr>
			</thead>
			<tbody id="recentTableBody">
				<!-- Recent products will be loaded here -->
			</tbody>
		</table>
		<div id="noRecent" class="no-data" style="display: none;">
			<p>No products found.</p>
		</div>
	</div>
</div>
<?php
$additionalJS = '<script>
// Load dashboard data on page load
document.addEventListener("DOMContentLoaded", function() {
    loadDashboardData();
});

// Load dashboard statistics and recent data
async function loadDashboardData() {
    try {
        await Promise.all([
            loadStatistics(),
            loadRecentProducts()
        ]);
    } catch (error) {
        console.error("Error loading dashboard data:", error);
        showAlert("Error loading dashboard data: " + error.message, "error");
    }
}

// Load statistics from Firestore
async function loadStatistics() {
    const db = firebase.firestore();
    
    try {
        // Get total sellers
        const sellersSnapshot = await db.collection("users")
            .where("userType", "==", "seller")
            .get();
        document.getElementById("totalSellers").textContent = sellersSnapshot.size;
        
        // Get total products
        const productsSnapshot = await db.collection("products").get();
        document.getElementById("totalProducts").textContent = productsSnapshot.size;
        
        // For now, set placeholder values for orders and sales
        // These would be calculated from an orders collection
        document.getElementById("pendingOrders").textContent = "0";
        document.getElementById("totalSales").textContent = "₱0.00";
        
    } catch (error) {
        console.error("Error loading statistics:", error);
        showAlert("Error loading statistics: " + error.message, "error");
    }
}

// Load recent products
async function loadRecentProducts() {
    try {
        const db = firebase.firestore();
        const snapshot = await db.collection("products")
            .orderBy("createdAt", "desc")
            .limit(5)
            .get();
        
        const loading = document.getElementById("loadingRecent");
        const table = document.getElementById("recentTable");
        const noData = document.getElementById("noRecent");
        const tbody = document.getElementById("recentTableBody");
        
        loading.style.display = "none";
        
        if (snapshot.empty) {
            table.style.display = "none";
            noData.style.display = "block";
            return;
        }
        
        table.style.display = "table";
        noData.style.display = "none";
        tbody.innerHTML = "";
        
        snapshot.forEach(doc => {
            const product = doc.data();
            const row = document.createElement("tr");
            const createdDate = formatDate(product.createdAt);
            const imageIcon = getCategoryIcon(product.category);
            
            row.innerHTML = `
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="product-image">
                            ${product.imageData ? 
                                `<img src="${product.imageData}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">` : 
                                `<i class="fas ${imageIcon}"></i>`
                            }
                        </div>
                        <div>
                            <div style="font-weight: 600;">${product.name || "Unnamed Product"}</div>
                            <div style="font-size: 12px; color: #666;">${product.category}</div>
                        </div>
                    </div>
                </td>
                <td>${product.category}</td>
                <td>${product.description || "No description"}</td>
                <td>${createdDate}</td>
            `;
            tbody.appendChild(row);
        });
        
    } catch (error) {
        console.error("Error loading recent products:", error);
        showAlert("Error loading recent products: " + error.message, "error");
    }
}

// Get category icon
function getCategoryIcon(category) {
    const icons = {
        "Vegetables": "fa-carrot",
        "Fruits": "fa-apple-alt",
        "Grains": "fa-seedling",
        "Dairy": "fa-cheese",
        "Meat": "fa-drumstick-bite",
        "Seafood": "fa-fish",
        "Herbs": "fa-leaf",
        "Other": "fa-box"
    };
    return icons[category] || "fa-box";
}

// Format date
function formatDate(timestamp) {
    if (!timestamp) return "N/A";
    const date = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
    return date.toLocaleDateString();
}

// Check authentication
firebase.auth().onAuthStateChanged(function(user) {
    if (!user) {
        window.location.href = "auth/login.php";
    }
});
</script>';
require_once __DIR__ . '/includes/footer.php';
?>
