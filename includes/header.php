<?php
session_start();
require_once __DIR__ . '/../config/firebase-config.php';

$scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
$depth = $scriptDir === '' ? 0 : count(array_filter(explode('/', $scriptDir)));
$assetPrefix = str_repeat('../', $depth);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo isset($pageTitle) ? $pageTitle . ' - Green Trade Admin' : 'Green Trade Admin'; ?></title>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $assetPrefix; ?>assets/css/styles.css">
	<?php if (isset($additionalCSS)) echo $additionalCSS; ?>
</head>
<body>
	<?php if (isset($showHeader) && $showHeader): ?>
	<div class="header">
		<h1>Green Trade</h1>
		<button class="logout-btn" onclick="showLogoutModal()">Logout</button>
	</div>
	<div class="nav">
		<ul>
			<li><a href="<?php echo $assetPrefix; ?>dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>Dashboard</a></li>
			<li><a href="<?php echo $assetPrefix; ?>manage-sellers.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage-sellers.php') ? 'class="active"' : ''; ?>>Manage Sellers</a></li>
			<li><a href="<?php echo $assetPrefix; ?>manage-products.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage-products.php') ? 'class="active"' : ''; ?>>Manage Products</a></li>
			<li><a href="<?php echo $assetPrefix; ?>orders.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'class="active"' : ''; ?>>Orders</a></li>
		</ul>
	</div>
	<?php endif; ?>
