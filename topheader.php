<?php  
require "Sessionb.php";
$username = $_SESSION['username'];
$role = $_SESSION['user_type'];
// ✅ Detect current file for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard - Ijaz Book Store</title>

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Nunito:400,600|Poppins:400,500,600" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Main CSS -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
/* General body font and background */
body {
    background: #e5f4f9;
    font-family: 'Open Sans', sans-serif;
    color: #045E70; /* Default text color for forms */
}

/* Page Titles */
.page-title, h1, h2, h3, h4, h5, h6 {
    color: #045E70;
    font-weight: bold;
}

/* Card styling for forms */
.card, .card-premium {
    border-radius: 15px;
    border: none;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    background: #ffffff;
}

/* Form labels */
.form-label {
    font-weight: bold;
    color: #0890A6;
    font-size: 0.875rem;
}

/* Inputs, selects, textareas */
input.form-control,
select.form-control,
textarea.form-control {
    border: 1px solid #045E70;
    border-radius: 5px !important;
    font-size: 0.9rem;
    color: #045E70;
    background-color: #fff;
    transition: border 0.3s, box-shadow 0.3s;
}

input.form-control:focus,
select.form-control:focus,
textarea.form-control:focus {
    border-color: #045E70;
    box-shadow: 0 0 6px rgba(4,94,112,0.3);
    outline: none;
}

/* Apply same design to input, select, and textarea fields */
input.form-control,
select.form-control,
select.form-select,
textarea.form-control {
    border: 1px solid #045E70;
    border-radius: 5px !important;
    font-size: 0.9rem;
    color: #045E70;
    background-color: #fff;
    transition: border 0.3s, box-shadow 0.3s;
}

/* Focus effect (same for all fields) */
input.form-control:focus,
select.form-control:focus,
select.form-select:focus,
textarea.form-control:focus {
    border-color: #045E70;
    box-shadow: 0 0 6px rgba(4,94,112,0.3);
    outline: none;
}

/* Optional — make placeholder / first option look lighter */
select.form-select option[disabled] {
    color: #999;
}

/* Optional — custom dropdown arrow in your theme color */
select.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23045E70' viewBox='0 0 16 16'%3E%3Cpath d='M3 6l5 5 5-5H3z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 12px;
    padding-right: 2rem;
}


/* Buttons */
.btn, button {
    border-radius: 12px !important;
    font-weight: 500;
    padding: 8px 20px;
    transition: 0.3s;
    font-size: 0.9rem;
}

.btn-primary, button.btn-primary {
    background-color: #045E70;
    color: #ffffff;
    border-color: #045E70;
}

.btn-primary:hover, button.btn-primary:hover {
    opacity: 0.9;
}

.btn-secondary, button.btn-secondary {
    background-color: #6c757d;
    color: #ffffff;
    border-radius: 12px;
    border: 1px solid #6c757d;
}

.btn-secondary:hover {
    opacity: 0.9;
}

/* Radio & Check buttons */
.btn-outline-primary {
    color: #045E70 !important;
    border-color: #045E70 !important;
    border-radius: 6px !important;
}

.btn-outline-primary:hover {
    background-color: #045E70 !important;
    color: #fff !important;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #045E70 !important;
    color: #fff !important;
    border-color: #045E70 !important;
}

/* Alerts */
.alert {
    border-radius: 8px;
    font-weight: 500;
}

/* Readonly boxes like Current Balance or Total */
#balance, .total, #net_price {
    background: #f1f6f9;
    font-weight: 600;
    text-align: right;
    border-radius: 10px;
    border: 1px solid #045E70;
    padding: 6px 12px;
    color: #045E70;
}

/* Dropdown / search results */
.result-box, .list-group-item {
    border: 1px solid #045E70;
    border-radius: 8px;
    background: #fff;
    color: #045E70;
    font-size: 0.85rem;
}

.result-box .list-group-item:hover {
    background-color: #e0f2f7;
    cursor: pointer;
}

/* Remove button for rows */
.removeRow {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 0.9rem;
    border-radius: 50%;
    padding: 6px 10px;
    line-height: 1;
    /* background-color: #045E70; */
    color: #fff;
    border: none;
}
.removeRow:hover {
    opacity: 0.9;
    cursor: pointer;
}

    /* ===== Navbar Custom Styling ===== */
    .navbar {
      padding: 0.4rem 1rem;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .navbar .nav-link {
      font-size: 0.9rem;
      padding: 0.4rem 0.8rem;
      color: #444;
      margin: 0 3px;  
    }

    .navbar .nav-link.active,
    .navbar .dropdown-menu .dropdown-item.active {
      color: #1CA1C2 !important;
      font-weight: 600;
      background: transparent;
    }

    .navbar .dropdown-menu {
      border-radius: 10px;
      border: none;
      box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    }

    .navbar-brand img {
      max-height: 45px;
    }

    /* Profile image */
    .navbar .dropdown-toggle img {
      border: 2px solid #ddd;
    }
    .btn{
       border-radius: 5px !important; 
    } 
    .dataTables_wrapper .dataTables_filter{
        padding-bottom: 0.2cm !important;
    }
  </style>
</head>

<body>
<header>
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">

      <!-- Brand -->
      <a href="index.php" class="navbar-brand fw-bold d-flex align-items-center">
        <img src="assets/img/Ijaz_logo.png" alt="Logo">
      </a>

      <!-- Mobile toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar items -->
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
          
          <?php if($role == 2) { // Admin ?>
            
            <li class="nav-item"><a class="nav-link <?= ($current_page=='index.php'?'active':'') ?>" href="index.php">Dashboard</a></li>
            
            <!-- Control Panel -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle <?= in_array($current_page,['authordata.php','pub_data.php','cate_data.php','vendor_data.php','client_data.php'])?'active':'' ?>" href="#" id="controlDropdown" data-bs-toggle="dropdown">Control Panel</a>
              <ul class="dropdown-menu shadow">
                <li><a class="dropdown-item <?= ($current_page=='authordata.php'?'active':'') ?>" href="authordata.php">Author</a></li>
                <li><a class="dropdown-item <?= ($current_page=='pub_data.php'?'active':'') ?>" href="pub_data.php">Publisher</a></li>
                <li><a class="dropdown-item <?= ($current_page=='cate_data.php'?'active':'') ?>" href="cate_data.php">Category</a></li>
                <li><a class="dropdown-item <?= ($current_page=='vendor_data.php'?'active':'') ?>" href="vendor_data.php">Vendor</a></li>
                <li><a class="dropdown-item <?= ($current_page=='client_data.php'?'active':'') ?>" href="client_data.php">Clients</a></li>
                <li><a class="dropdown-item <?= ($current_page=='user_for.php'?'active':'') ?>" href="user_for.php">User</a></li>
              </ul>
            </li>

            <li class="nav-item"><a class="nav-link <?= ($current_page=='tables.php'?'active':'') ?>" href="tables.php">Book Insert</a></li>
            <li class="nav-item"><a class="nav-link <?= ($current_page=='book.php'?'active':'') ?>" href="book.php">Purchase</a></li>
            <li class="nav-item"><a class="nav-link <?= ($current_page=='sale_book.php'?'active':'') ?>" href="sale_book.php">Sale</a></li>
            <li class="nav-item"><a class="nav-link <?= ($current_page=='salereturn.php'?'active':'') ?>" href="salereturn.php">Sales Return</a></li>

            <!-- Transactions -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle <?= in_array($current_page,['vendor_account.php','vendor_payment.php','client_account.php','client_payment.php'])?'active':'' ?>" href="#" id="transactionsDropdown" data-bs-toggle="dropdown">Transactions</a>
              <ul class="dropdown-menu shadow">
                <li><a class="dropdown-item <?= ($current_page=='vendor_account.php'?'active':'') ?>" href="vendor_account.php">Vendor Account</a></li>
                <li><a class="dropdown-item <?= ($current_page=='vendor_payment.php'?'active':'') ?>" href="vendor_payment.php">Vendor Payments</a></li>
                <li><a class="dropdown-item <?= ($current_page=='client_account.php'?'active':'') ?>" href="client_account.php">Client Account</a></li>
                <li><a class="dropdown-item <?= ($current_page=='client_payment.php'?'active':'') ?>" href="client_payment.php">Client Payments</a></li>
              </ul>
            </li>

            <li class="nav-item"><a class="nav-link <?= ($current_page=='funds.php'?'active':'') ?>" href="funds.php">Funds & Expenses</a></li>

         <?php } elseif($role == 0) { // Staff ?>
            
    <li class="nav-item"><a class="nav-link <?= ($current_page=='book.php'?'active':'') ?>" href="book.php">Purchase</a></li>
    <li class="nav-item"><a class="nav-link <?= ($current_page=='sale_book.php'?'active':'') ?>" href="sale_book.php">Sale</a></li>
    <li class="nav-item"><a class="nav-link <?= ($current_page=='funds.php'?'active':'') ?>" href="funds.php">Funds & Expenses</a></li>

<?php } ?>


          <!-- Reporting (both roles) -->
          <li class="nav-item"><a class="nav-link <?= ($current_page=='reporting.php'?'active':'') ?>" href="reporting.php">Reporting</a></li>
        </ul>

        <!-- Pending Drafts Icon -->
        <ul class="navbar-nav ms-auto">
          <?php
            $pending_count = 0;
            $pc1 = mysqli_query($conn, "SELECT COUNT(DISTINCT invoice_no) AS cnt FROM sale_invoice WHERE status_invoice = 0");
            if ($pc1) $pending_count += mysqli_fetch_assoc($pc1)['cnt'];
            $pc2 = mysqli_query($conn, "SELECT COUNT(DISTINCT invoice_no) AS cnt FROM purchase_invoice WHERE status_invoice = 0");
            if ($pc2) $pending_count += mysqli_fetch_assoc($pc2)['cnt'];
          ?>
          <li class="nav-item">
            <a class="nav-link position-relative <?= ($current_page=='pending_invoices.php'?'active':'') ?>" href="pending_invoices.php" title="Pending Drafts">
              <i class="bi bi-hourglass-split" style="font-size: 1.2rem;"></i>
              <?php if ($pending_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:#f0ad4e; font-size:0.65rem;">
                  <?= $pending_count ?>
                </span>
              <?php endif; ?>
            </a>
          </li>
        </ul>

        <!-- Profile Dropdown -->
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" data-bs-toggle="dropdown">
              <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle" width="35" height="35">
              <span class="ms-2 fw-semibold"><?= $username ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow">
              <li><h6 class="dropdown-header text-primary"><?= $username ?></h6></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
            </ul>
          </li>
        </ul>

      </div>
    </div>
  </nav>
</header>

<main id="main" class="main">
<!-- Bootstrap JS (REQUIRED for dropdown, navbar toggle, modals, etc.) -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
