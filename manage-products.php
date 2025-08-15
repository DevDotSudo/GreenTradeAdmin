<?php
$pageTitle = 'Manage Products';
$showHeader = true;
$showFooter = true;
$showLogoutModal = true;
$showDeleteModal = true;
require_once __DIR__ . '/includes/header.php';
?>
<div class="main-content">
	<div class="page-header">
		<h2>Manage Products</h2>
	</div>

	<div class="controls">
		<div class="search-box">
			<input type="text" placeholder="Search products by name, category, or seller..." id="searchInput">
		</div>
		<div class="filter-group">
			<label for="categoryFilter">Category</label>
			<select id="categoryFilter">
				<option value="">All</option>
			</select>
		</div>
	</div>

	<div class="products-section">
		<h3>Product List</h3>
		<div id="loadingProducts" class="loading">Loading products...</div>
		<table class="data-table" id="productsTable" style="display: none;">
			<thead>
				<tr>
					<th>Product</th>
					<th>Category</th>
					<th>Description</th>
					<th>Created Date</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody id="productsTableBody">
				<!-- Products will be loaded here dynamically -->
			</tbody>
		</table>
		<div id="noProducts" class="no-data" style="display: none;">
			<p>No products found.</p>
		</div>
	</div>
</div>

<!-- View Product Modal -->
<div id="viewProductModal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<h3>Product Details</h3>
			<span class="close" onclick="closeViewProductModal()">&times;</span>
		</div>
		<div class="modal-body" id="viewProductContent">
			<!-- Product details will be loaded here -->
		</div>
		<div class="modal-footer">
			<button class="btn-secondary" onclick="closeViewProductModal()">Close</button>
		</div>
	</div>
</div>

<?php
$additionalJS = '<script>
let products = [];
let currentProductId = null;

// Load products on page load
document.addEventListener("DOMContentLoaded", function() {
    loadProducts();
});

// Load products from Firestore
async function loadProducts() {
    try {
        const db = firebase.firestore();
        const snapshot = await db.collection("products").get();
        
        products = [];
        snapshot.forEach(doc => {
            products.push({
                id: doc.id,
                ...doc.data()
            });
        });
        
        displayProducts();
        updateCategoryFilter();
    } catch (error) {
        console.error("Error loading products:", error);
        showAlert("Error loading products: " + error.message, "error");
    }
}

// Display products in table
function displayProducts() {
    const tbody = document.getElementById("productsTableBody");
    const loading = document.getElementById("loadingProducts");
    const table = document.getElementById("productsTable");
    const noData = document.getElementById("noProducts");
    
    loading.style.display = "none";
    
    if (products.length === 0) {
        table.style.display = "none";
        noData.style.display = "block";
        return;
    }
    
    table.style.display = "table";
    noData.style.display = "none";
    
    tbody.innerHTML = "";
    
    products.forEach(product => {
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
            <td>
                <button class="action-btn view-btn" onclick="viewProduct(\'${product.id}\')">View</button>
                <button class="action-btn delete-btn" onclick="deleteProduct(\'${product.id}\')">Remove</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Update category filter options
function updateCategoryFilter() {
    const categoryFilter = document.getElementById("categoryFilter");
    const categories = [...new Set(products.map(product => product.category))];
    
    // Clear existing options except "All"
    categoryFilter.innerHTML = \'<option value="">All</option>\';
    
    categories.forEach(category => {
        const option = document.createElement("option");
        option.value = category;
        option.textContent = category;
        categoryFilter.appendChild(option);
    });
}

// Filter table
function filterTable() {
    const searchTerm = (document.getElementById("searchInput").value || "").toLowerCase();
    const categoryFilter = document.getElementById("categoryFilter").value;
    const rows = document.querySelectorAll("#productsTableBody tr");
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const categoryText = row.querySelector("td:nth-child(2)")?.textContent || "";
        let show = true;
        
        if (searchTerm && !text.includes(searchTerm)) show = false;
        if (categoryFilter && categoryText !== categoryFilter) show = false;
        
        row.style.display = show ? "" : "none";
    });
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

// View product details
async function viewProduct(id) {
    const product = products.find(p => p.id === id);
    if (!product) return;
    
    const content = document.getElementById("viewProductContent");
    const createdDate = formatDate(product.createdAt);
    const imageIcon = getCategoryIcon(product.category);
    
    content.innerHTML = `
        <div class="product-details">
            <div class="detail-row">
                <strong>Name:</strong> ${product.name || "Unnamed Product"}
            </div>
            <div class="detail-row">
                <strong>Category:</strong> ${product.category}
            </div>
            <div class="detail-row">
                <strong>Description:</strong> ${product.description || "No description"}
            </div>
            <div class="detail-row">
                <strong>Created:</strong> ${createdDate}
            </div>
            ${product.imageData ? `
            <div class="detail-row">
                <strong>Image:</strong>
                <div style="margin-top: 10px;">
                    <img src="${product.imageData}" style="max-width: 300px; max-height: 300px; border-radius: 8px;">
                </div>
            </div>
            ` : `
            <div class="detail-row">
                <strong>Image:</strong> <i class="fas ${imageIcon}"></i> No image uploaded
            </div>
            `}
        </div>
    `;
    
    document.getElementById("viewProductModal").style.display = "block";
}

// Delete product
function deleteProduct(id) {
    currentProductId = id;
    showDeleteModal();
}

// Confirm delete
async function confirmDelete() {
    try {
        const db = firebase.firestore();
        await db.collection("products").doc(currentProductId).delete();
        showAlert("Product removed successfully!", "success");
        closeDeleteModal();
        loadProducts();
    } catch (error) {
        console.error("Error removing product:", error);
        showAlert("Error removing product: " + error.message, "error");
    }
}

// Close modals
function closeViewProductModal() {
    document.getElementById("viewProductModal").style.display = "none";
}

// Event listeners
document.getElementById("searchInput").addEventListener("input", filterTable);
document.getElementById("categoryFilter").addEventListener("change", filterTable);
</script>';
require_once __DIR__ . '/includes/footer.php';
?>
