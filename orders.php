<?php
$pageTitle = 'Orders History';
$showHeader = true;
$showFooter = true;
$showLogoutModal = true;
$showDeleteModal = false;
require_once __DIR__ . '/includes/header.php';
?>
<div class="main-content">
	<div class="page-header">
		<h2>Orders History</h2>
	</div>

	<div class="controls">
		<div class="search-box">
			<input type="text" placeholder="Search orders by ID, customer name, or seller..." id="searchInput">
		</div>
		<div class="filter-group">
			<label for="statusFilter">Status</label>
			<select id="statusFilter">
				<option value="">All</option>
				<option value="Pending">Pending</option>
				<option value="Processing">Processing</option>
				<option value="Delivered">Delivered</option>
				<option value="Cancelled">Cancelled</option>
			</select>
		</div>
		<div class="filter-group">
			<label for="dateFilter">Date</label>
			<select id="dateFilter">
				<option value="">All</option>
				<option value="today">Today</option>
				<option value="week">This Week</option>
				<option value="month">This Month</option>
			</select>
		</div>
	</div>

	<div class="orders-section">
		<h3>Order List</h3>
		<div id="loadingOrders" class="loading">Loading orders...</div>
		<table class="data-table" id="ordersTable" style="display: none;">
			<thead>
				<tr>
					<th>Order ID</th>
					<th>Date</th>
					<th>Customer</th>
					<th>Seller</th>
					<th>Items</th>
					<th>Total</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody id="ordersTableBody">
			</tbody>
		</table>
		<div id="noOrders" class="no-data" style="display: none;">
			<p>No orders found.</p>
		</div>
	</div>
</div>

<?php
$additionalJS = <<<'SCRIPT'
<script>
let orders = [];

document.addEventListener("DOMContentLoaded", function() {
    initOrdersListener();
    document.getElementById("searchInput").addEventListener("input", filterTable);
    document.getElementById("statusFilter").addEventListener("change", filterTable);
    document.getElementById("dateFilter").addEventListener("change", filterTable);
});

function initOrdersListener() {
    const loading = document.getElementById("loadingOrders");
    const table = document.getElementById("ordersTable");
    const noData = document.getElementById("noOrders");
    loading.style.display = "block";
    table.style.display = "none";
    noData.style.display = "none";

    try {
        const db = firebase.firestore();
        db.collection("orders").orderBy("orderDate", "desc").onSnapshot(snapshot => {
            orders = snapshot.docs.map(d => ({ id: d.id, ...d.data() }));
            displayOrders();
        }, err => {
            console.error('Orders listener error', err);
            loading.style.display = 'none';
            noData.style.display = 'block';
        });
    } catch (err) {
        console.error('Error initializing orders listener', err);
        loading.style.display = 'none';
        noData.style.display = 'block';
    }
}

function displayOrders() {
    const tbody = document.getElementById("ordersTableBody");
    const loading = document.getElementById("loadingOrders");
    const table = document.getElementById("ordersTable");
    const noData = document.getElementById("noOrders");
    loading.style.display = "none";

    if (!orders || orders.length === 0) {
        table.style.display = "none";
        noData.style.display = "block";
        return;
    }

    table.style.display = "table";
    noData.style.display = "none";
    tbody.innerHTML = "";

    orders.forEach(order => {
        const row = document.createElement("tr");

        const orderDate = formatDate(order.orderDate);
        const customer = (order.shippingDetails && order.shippingDetails.name) || order.buyerName || "N/A";
        const sellers = (order.items || []).map(i => i.sellerName).filter(Boolean);
        const uniqueSellers = [...new Set(sellers)].join(", ") || "N/A";
        const itemCount = (order.items || []).reduce((s, i) => s + (i.quantity || 0), 0);
        const total = typeof order.totalAmount !== 'undefined' ? order.totalAmount : (order.total || 0);
        const subtotal = typeof order.subtotal !== 'undefined' ? order.subtotal : 0;
        const deliveryFee = typeof order.deliveryFee !== 'undefined' ? order.deliveryFee : 0;
        const status = order.status || "Pending";

        row.innerHTML = `
            <td>${escapeHtml(order.id)}</td>
            <td>${escapeHtml(orderDate)}</td>
            <td>${escapeHtml(customer)}</td>
            <td>${escapeHtml(uniqueSellers)}</td>
            <td><button class="details-btn" data-id="${escapeHtml(order.id)}">View (${itemCount})</button></td>
            <td>₱${formatNumber(total)}</td>
            <td><span class="status-badge status-${status.toLowerCase()}">${escapeHtml(status)}</span></td>
        `;

        tbody.appendChild(row);

        const detailsRow = document.createElement('tr');
        detailsRow.className = 'order-details-row';
        detailsRow.style.display = 'none';
        const detailsCell = document.createElement('td');
        detailsCell.colSpan = 7;
        detailsCell.innerHTML = renderOrderDetails(order);
        detailsRow.appendChild(detailsCell);
        tbody.appendChild(detailsRow);
    });

    document.querySelectorAll('.details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tr = this.closest('tr');
            const detailsRow = tr.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('order-details-row')) {
                detailsRow.style.display = detailsRow.style.display === 'none' ? '' : 'none';
            }
        });
    });

    filterTable();
}

function renderOrderDetails(order) {
    const shipping = order.shippingDetails || {};
    let html = '<div class="order-details">';
    html += `<div><strong>Buyer:</strong> ${escapeHtml(order.buyerName || shipping.name || 'N/A')} (${escapeHtml(order.buyerId || '')})</div>`;
    html += `<div><strong>Payment:</strong> ${escapeHtml(order.paymentMethod || '')}</div>`;
    html += `<div><strong>Shipping:</strong> ${escapeHtml(shipping.name || '')} • ${escapeHtml(shipping.address || '')} • ${escapeHtml(shipping.phone || '')}</div>`;
    html += `<div><strong>Subtotal:</strong> ₱${formatNumber(order.subtotal || 0)} • <strong>Delivery:</strong> ₱${formatNumber(order.deliveryFee || 0)} • <strong>Total:</strong> ₱${formatNumber(order.totalAmount || order.total || 0)}</div>`;
    html += '<div class="items-list"><strong>Items:</strong><ul>';
    (order.items || []).forEach(it => {
        const itemTotal = (it.quantity || 0) * (it.price || 0);
        html += `<li>${escapeHtml(it.name || '')} — ${escapeHtml(it.quantity || 0)} × ₱${formatNumber(it.price || 0)} = ₱${formatNumber(itemTotal)} <div class="small">Seller: ${escapeHtml(it.sellerName || '')}</div></li>`;
    });
    html += '</ul></div>';
    html += '</div>';
    return html;
}

function formatDate(timestamp) {
    if (!timestamp) return 'N/A';
    try {
        const d = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
        return d.toLocaleString('en-PH', { timeZone: 'Asia/Manila' });
    } catch (e) {
        return 'N/A';
    }
}

function formatNumber(n) { return Number(n || 0).toLocaleString('en-PH'); }

function escapeHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s).replace(/[&<>"']/g, function(m) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m];
    });
}

function filterTable() {
    const searchTerm = (document.getElementById('searchInput').value || '').toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const rows = Array.from(document.querySelectorAll('#ordersTableBody tr')).filter(r => !r.classList.contains('order-details-row'));

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const statusText = row.querySelector('.status-badge')?.textContent?.trim() || '';
        let show = true;

        if (searchTerm) {
            if (!text.includes(searchTerm)) show = false;
        }
        if (statusFilter && statusText !== statusFilter) show = false;
        if (dateFilter) {
            const dateTd = row.children[1]?.textContent || '';
            const orderDate = new Date(dateTd);
            const now = new Date();
            if (isNaN(orderDate.getTime())) {
                show = false;
            } else if (dateFilter === 'today') {
                show = show && isSameDay(orderDate, now);
            } else if (dateFilter === 'week') {
                const weekAgo = new Date(); weekAgo.setDate(now.getDate() - 7);
                show = show && orderDate >= weekAgo;
            } else if (dateFilter === 'month') {
                const monthAgo = new Date(); monthAgo.setMonth(now.getMonth() - 1);
                show = show && orderDate >= monthAgo;
            }
        }

        row.style.display = show ? '' : 'none';
        const detailsRow = row.nextElementSibling;
        if (detailsRow && detailsRow.classList.contains('order-details-row')) detailsRow.style.display = show ? detailsRow.style.display : 'none';
    });

    const visible = Array.from(document.querySelectorAll('#ordersTableBody tr')).some(r => r.style.display !== 'none' && !r.classList.contains('order-details-row'));
    document.getElementById('noOrders').style.display = visible ? 'none' : 'block';
    document.getElementById('ordersTable').style.display = visible ? 'table' : 'none';
}

function isSameDay(d1, d2) { return d1.getFullYear() === d2.getFullYear() && d1.getMonth() === d2.getMonth() && d1.getDate() === d2.getDate(); }
</script>
SCRIPT;
require_once __DIR__ . '/includes/footer.php';
?>
