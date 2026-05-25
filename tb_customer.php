<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "sewajas");

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// TAMBAH
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_customer'];
    $hp   = $_POST['no_hp'];
    $ktp  = $_POST['KTP'];

    mysqli_query($conn, "INSERT INTO tb_customer 
        (nama_customer, no_hp, KTP) 
        VALUES ('$nama','$hp','$ktp')");

    $_SESSION['notif'] = "tambah";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM tb_customer WHERE id_customer='$id'");

    $_SESSION['notif'] = "hapus";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// EDIT
$edit = false;
if (isset($_GET['edit'])) {
    $edit = true;
    $id = $_GET['edit'];
    $data_edit = mysqli_query($conn, "SELECT * FROM tb_customer WHERE id_customer='$id'");
    $e = mysqli_fetch_array($data_edit);
}

// UPDATE
if (isset($_POST['update'])) {
    $id   = $_POST['id_customer'];
    $nama = $_POST['nama_customer'];
    $hp   = $_POST['no_hp'];
    $ktp  = $_POST['KTP'];

    mysqli_query($conn, "UPDATE tb_customer SET
        nama_customer='$nama',
        no_hp='$hp',
        KTP='$ktp'
        WHERE id_customer='$id'");

    $_SESSION['notif'] = "update";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Data Customer</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
    margin: 0;
    background: #ecf0f5;
    font-family: 'Segoe UI', sans-serif;
}

/* WRAPPER */
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
    bottom: 0;
    left: 0;
    padding-top: 20px;
}

.sidebar h3 {
    text-align: center;
    margin-bottom: 20px;
}

.sidebar a {
    display: block;
    padding: 12px 20px;
    color: white;
    text-decoration: none;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.15);
}

/* MAIN */
.main-content {
    margin-left: 250px;
    width: calc(100% - 250px);
}

/* TOPBAR */
.topbar {
    height: 60px;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* CONTENT */
.content {
    padding: 30px;
}
</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>ADMIN</h3>
        <a href="dashboardadmin.php"><i class="fa fa-house"></i> Dashboard</a>
        <a href="tb_jas.php"><i class="fa fa-shirt"></i> Data Jas</a>
        <a href="tb_customer.php"><i class="fa fa-user"></i> Customer</a>
        <a href="tb_order.php"><i class="fa fa-cart-shopping"></i> Order</a>
        <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
    </div>

    <!-- MAIN -->
    <div class="main-content">

        <div class="topbar">
            <h5>Data Customer</h5>
            <div>
                <?= $_SESSION['username']; ?>
            </div>
        </div>

        <div class="content">

        <!-- NOTIF -->
        <?php if (isset($_SESSION['notif'])): ?>
        <div id="notif" class="alert 
            <?= $_SESSION['notif'] == 'tambah' ? 'alert-success' : 
               ($_SESSION['notif'] == 'update' ? 'alert-warning' : 'alert-danger') ?>">

            <?php
            if ($_SESSION['notif'] == "tambah") echo "Data berhasil ditambahkan!";
            elseif ($_SESSION['notif'] == "update") echo "Data berhasil diupdate!";
            elseif ($_SESSION['notif'] == "hapus") echo "Data berhasil dihapus!";
            ?>

        </div>
        <?php unset($_SESSION['notif']); endif; ?>

        <!-- FORM -->
        <div class="card p-4 mb-4">
        <form method="POST">

        <?php if ($edit): ?>
        <input type="hidden" name="id_customer" value="<?= $e['id_customer']; ?>">
        <?php endif; ?>

        <input type="text" name="nama_customer" class="form-control mb-2"
        value="<?= $edit ? $e['nama_customer'] : '' ?>" placeholder="Nama Customer" required>

        <input type="text" name="no_hp" class="form-control mb-2"
        value="<?= $edit ? $e['no_hp'] : '' ?>" placeholder="No HP" required>

        <input type="text" name="KTP" class="form-control mb-2"
        value="<?= $edit ? $e['KTP'] : '' ?>" placeholder="No KTP" required>

        <?php if ($edit): ?>
        <button type="submit" name="update" class="btn btn-warning">Update</button>
        <a href="?" class="btn btn-secondary">Batal</a>
        <?php else: ?>
        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        <?php endif; ?>

        </form>
        </div>

        <!-- SEARCH -->
        <div class="card p-3 mb-3">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2"
                placeholder="Cari nama customer / no hp / ktp..."
                value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button>

                <a href="tb_customer.php" class="btn btn-secondary ms-2">
                    Reset
                </a>
            </form>
        </div>

        <!-- TABEL -->
        <table class="table table-bordered">
        <tr>
        <th>No</th>
        <th>Nama Customer</th>
        <th>No HP</th>
        <th>KTP</th>
        <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];

            $data = mysqli_query($conn, "SELECT * FROM tb_customer 
                WHERE nama_customer LIKE '%$search%'
                OR no_hp LIKE '%$search%'
                OR KTP LIKE '%$search%'");
        } else {
            $data = mysqli_query($conn, "SELECT * FROM tb_customer");
        }

        while ($d = mysqli_fetch_array($data)) {
        ?>
        <tr>
        <td><?= $no++; ?></td>
        <td><?= $d['nama_customer']; ?></td>
        <td><?= $d['no_hp']; ?></td>
        <td><?= $d['KTP']; ?></td>
        <td>
        <a href="?edit=<?= $d['id_customer']; ?>" class="btn btn-sm btn-warning">Edit</a>
        <a href="?hapus=<?= $d['id_customer']; ?>" class="btn btn-sm btn-danger"
        onclick="return confirm('Yakin hapus data?')">Hapus</a>
        </td>
        </tr>
        <?php } ?>
        </table>

        </div>
    </div>

</div>

<!-- AUTO HILANG -->
<script>
setTimeout(function() {
    let notif = document.getElementById('notif');
    if (notif) {
        notif.style.transition = "opacity 0.5s";
        notif.style.opacity = "0";
        setTimeout(() => notif.remove(), 500);
    }
}, 3000);
</script>

</body>
</html>