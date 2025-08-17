<?php
$pageTitle = 'Manage Sellers';
$showHeader = true;
$showFooter = true;
$showLogoutModal = true;
$showDeleteModal = true;
require_once __DIR__ . '/includes/header.php';
?>
<div class="main-content">
	<div class="page-header">
		<h2>Manage Sellers</h2>
	</div>

	<div class="controls">
		<div class="search-box">
			<input type="text" placeholder="Search sellers by name, email, or location..." id="searchInput">
		</div>
		<div class="filter-group">
			<label for="locationFilter">Location</label>
			<select id="locationFilter">
				<option value="">All</option>
			</select>
		</div>
	</div>

	<div class="sellers-section">
		<h3>Seller List</h3>
		<div id="loadingSellers" class="loading">Loading sellers...</div>
		<table class="data-table" id="sellersTable" style="display: none;">
			<thead>
				<tr>
					<th>Seller</th>
					<th>Email</th>
					<th>Location</th>
					<th>Phone</th>
					<th>Joined Date</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody id="sellersTableBody">
			</tbody>
		</table>
		<div id="noSellers" class="no-data" style="display: none;">
			<p>No sellers found.</p>
		</div>
	</div>
</div>

<div id="viewSellerModal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<h3>Seller Details</h3>
			<span class="close" onclick="closeViewSellerModal()">&times;</span>
		</div>
		<div class="modal-body" id="viewSellerContent">
		</div>
		<div class="modal-footer">
			<button class="btn-secondary" onclick="closeViewSellerModal()">Close</button>
		</div>
	</div>
</div>

<?php
$additionalJS = '<script>
let sellers = [];
let currentSellerId = null;

// Load sellers on page load
document.addEventListener("DOMContentLoaded", function() {
    loadSellers();
});

// Load sellers from Firestore
async function loadSellers() {
    try {
        const db = firebase.firestore();
        const snapshot = await db.collection("users")
            .where("userType", "==", "seller")
            .get();
        
        sellers = [];
        snapshot.forEach(doc => {
            sellers.push({
                id: doc.id,
                ...doc.data()
            });
        });
        
        displaySellers();
        updateLocationFilter();
    } catch (error) {
        console.error("Error loading sellers:", error);
        showAlert("Error loading sellers: " + error.message, "error");
    }
}

// Display sellers in table
function displaySellers() {
    const tbody = document.getElementById("sellersTableBody");
    const loading = document.getElementById("loadingSellers");
    const table = document.getElementById("sellersTable");
    const noData = document.getElementById("noSellers");
    
    loading.style.display = "none";
    
    if (sellers.length === 0) {
        table.style.display = "none";
        noData.style.display = "block";
        return;
    }
    
    table.style.display = "table";
    noData.style.display = "none";
    
    tbody.innerHTML = "";
    
    sellers.forEach(seller => {
        const row = document.createElement("tr");
        const initials = getInitials(seller.name);
        const joinedDate = formatDate(seller.createdAt);
        
        row.innerHTML = `
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="seller-avatar">${initials}</div>
                    <div>
                        <div style="font-weight: 600;">${seller.name}</div>
                        <div style="font-size: 12px; color: #666;">${seller.phone}</div>
                    </div>
                </div>
            </td>
            <td>${seller.email}</td>
            <td>${seller.address}</td>
            <td>${seller.phone}</td>
            <td>${joinedDate}</td>
            <td>
                <button class="action-btn view-btn" onclick="viewSeller(\'${seller.id}\')">View</button>
                <button class="action-btn delete-btn" onclick="deleteSeller(\'${seller.id}\')">Remove</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Update location filter options
function updateLocationFilter() {
    const locationFilter = document.getElementById("locationFilter");
    const locations = [...new Set(sellers.map(seller => seller.address))];
    
    // Clear existing options except "All"
    locationFilter.innerHTML = \'<option value="">All</option>\';
    
    locations.forEach(location => {
        const option = document.createElement("option");
        option.value = location;
        option.textContent = location;
        locationFilter.appendChild(option);
    });
}

// Filter table
function filterTable() {
    const searchTerm = (document.getElementById("searchInput").value || "").toLowerCase();
    const locationFilter = document.getElementById("locationFilter").value;
    const rows = document.querySelectorAll("#sellersTableBody tr");
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const locationText = row.querySelector("td:nth-child(3)")?.textContent || "";
        let show = true;
        
        if (searchTerm && !text.includes(searchTerm)) show = false;
        if (locationFilter && locationText !== locationFilter) show = false;
        
        row.style.display = show ? "" : "none";
    });
}

// Get initials from name
function getInitials(name) {
    return name.split(" ").map(word => word.charAt(0)).join("").toUpperCase();
}

// Format date
function formatDate(timestamp) {
    if (!timestamp) return "N/A";
    const date = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
    return date.toLocaleDateString();
}

// View seller details
async function viewSeller(id) {
    const seller = sellers.find(s => s.id === id);
    if (!seller) return;
    
    const content = document.getElementById("viewSellerContent");
    const joinedDate = formatDate(seller.createdAt);
    
    content.innerHTML = `
        <div class="seller-details">
            <div class="detail-row">
                <strong>Name:</strong> ${seller.name}
            </div>
            <div class="detail-row">
                <strong>Email:</strong> ${seller.email}
            </div>
            <div class="detail-row">
                <strong>Phone:</strong> ${seller.phone}
            </div>
            <div class="detail-row">
                <strong>Address:</strong> ${seller.address}
            </div>
            <div class="detail-row">
                <strong>Joined:</strong> ${joinedDate}
            </div>
        </div>
    `;
    
    document.getElementById("viewSellerModal").style.display = "block";
}

// Delete seller
function deleteSeller(id) {
    currentSellerId = id;
    showDeleteModal();
}

// Confirm delete
async function confirmDelete() {
    try {
        const db = firebase.firestore();
        await db.collection("users").doc(currentSellerId).delete();
        showAlert("Seller removed successfully!", "success");
        closeDeleteModal();
        loadSellers();
    } catch (error) {
        console.error("Error removing seller:", error);
        showAlert("Error removing seller: " + error.message, "error");
    }
}

// Close modals
function closeViewSellerModal() {
    document.getElementById("viewSellerModal").style.display = "none";
}

// Event listeners
document.getElementById("searchInput").addEventListener("input", filterTable);
document.getElementById("locationFilter").addEventListener("change", filterTable);
</script>';
require_once __DIR__ . '/includes/footer.php';
?>
