<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
    margin: 0;
    background: #ecf0f5;
    font-family: 'Segoe UI', sans-serif;
}

/* LAYOUT */
.wrapper {
    display: flex;
    min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
    width: 250px;
    background: #16a085;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    padding-top: 20px;
}

.sidebar h3 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: bold;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 15px 25px;
    transition: 0.2s;
    font-size: 15px;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.15);
    padding-left: 30px;
}

.sidebar i {
    width: 25px;
}

/* CONTENT */
.main-content {
    margin-left: 250px;
    width: calc(100% - 250px);
}

/* TOPBAR */
.topbar {
    height: 60px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.topbar .title {
    font-weight: bold;
    font-size: 20px;
    color: #16a085;
}

/* USER INFO */
.user-info {
    font-size: 14px;
}

.user-info a {
    text-decoration: none;
    color: #e74c3c;
    font-weight: 600;
}

/* CONTENT AREA */
.content {
    padding: 30px;
}

/* CARD MENU */
.menu-card {
    border-radius: 12px;
    color: white;
    padding: 20px;
    height: 170px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    transition: 0.25s ease;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.menu-card h5 {
    font-size: 17px;
    font-weight: 600;
}

.menu-card p {
    font-size: 13px;
    margin: 5px 0;
}

.menu-card i {
    position: absolute;
    right: 15px;
    bottom: 15px;
    font-size: 35px;
    opacity: 0.2;
}

.menu-card a {
    font-size: 13px;
    text-decoration: none;
    color: white;
    font-weight: bold;
}

/* WARNA */
.bg-blue { background: #2980b9; }
.bg-orange { background: #f39c12; }
.bg-green { background: #27ae60; }

/* RESPONSIVE */
@media(max-width: 768px){

    .sidebar{
        width: 100%;
        height: auto;
        position: relative;
    }

    .main-content{
        margin-left: 0;
        width: 100%;
    }

}
</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>ADMIN</h3>

        <a href="index.php">
            <i class="fa fa-house"></i> Dashboard
        </a>

        <a href="tb_jas.php">
            <i class="fa fa-shirt"></i> Data Jas
        </a>

        <a href="tb_customer.php">
            <i class="fa fa-user"></i> Data Customer
        </a>

        <a href="tb_order.php">
            <i class="fa fa-cart-shopping"></i> Order
        </a>

        <a href="logout.php">
            <i class="fa fa-right-from-bracket"></i> Logout
        </a>
    </div>

    <!-- MAIN -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="title">Dashboard Admin</div>

            <div class="user-info">
                Login sebagai :
                <b><?php echo $username; ?></b>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="menu-card bg-blue">
                        <div>
                            <h5>Data Jas</h5>
                        </div>
                        <a href="tb_jas.php">Masuk →</a>
                        <i class="fa fa-shirt"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="menu-card bg-orange">
                        <div>
                            <h5>Data Customer</h5>
                        </div>
                        <a href="tb_customer.php">Masuk →</a>
                        <i class="fa fa-user"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="menu-card bg-green">
                        <div>
                            <h5>Order</h5>
                        </div>
                        <a href="tb_order.php">Masuk →</a>
                        <i class="fa fa-cart-shopping"></i>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>