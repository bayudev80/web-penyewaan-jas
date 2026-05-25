<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "sewajas");

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

/* ================= FUNCTION HITUNG TOTAL ================= */
function hitungTotal($conn, $id_jas, $tgl_sewa, $tgl_kembali){
    $q = mysqli_query($conn, "SELECT Harga_sewa FROM tb_jas WHERE id_jas='$id_jas'");
    $j = mysqli_fetch_array($q);
    $harga = $j['Harga_sewa'];

    $lama = floor((strtotime($tgl_kembali) - strtotime($tgl_sewa)) / 86400) + 1;

    if ($lama < 1) $lama = 1;

    $denda = 0;

    if ($lama > 3) {
        $denda = ($lama - 3) * 50000;
    }

    return $harga + $denda;
}

/* ================= TAMBAH ================= */
if (isset($_POST['simpan'])) {

    $id_customer = $_POST['id_customer'];
    $id_jas = $_POST['id_jas'];
    $tanggal_sewa = $_POST['tanggal_sewa'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $status = $_POST['status'];

    if ($tanggal_kembali < $tanggal_sewa) {
        $_SESSION['notif'] = "error";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    $total = hitungTotal($conn,$id_jas,$tanggal_sewa,$tanggal_kembali);

    mysqli_query($conn, "INSERT INTO tb_order
    (id_customer,id_jas,tanggal_sewa,tanggal_kembali,status,total_harga)
    VALUES
    ('$id_customer','$id_jas','$tanggal_sewa','$tanggal_kembali','$status','$total')");

    $_SESSION['notif'] = "tambah";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

/* ================= HAPUS ================= */
if (isset($_GET['hapus'])) {

    mysqli_query($conn, "DELETE FROM tb_order WHERE id_order='".$_GET['hapus']."'");

    $_SESSION['notif'] = "hapus";

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

/* ================= EDIT ================= */
$edit = false;

if (isset($_GET['edit'])) {

    $edit = true;

    $data_edit = mysqli_query($conn, "SELECT * FROM tb_order WHERE id_order='".$_GET['edit']."'");

    $e = mysqli_fetch_array($data_edit);
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    $id = $_POST['id_order'];
    $id_customer = $_POST['id_customer'];
    $id_jas = $_POST['id_jas'];
    $tanggal_sewa = $_POST['tanggal_sewa'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $status = $_POST['status'];

    if ($tanggal_kembali < $tanggal_sewa) {

        $_SESSION['notif'] = "error";

        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    $total = hitungTotal($conn,$id_jas,$tanggal_sewa,$tanggal_kembali);

    mysqli_query($conn, "UPDATE tb_order SET
        id_customer='$id_customer',
        id_jas='$id_jas',
        tanggal_sewa='$tanggal_sewa',
        tanggal_kembali='$tanggal_kembali',
        status='$status',
        total_harga='$total'
        WHERE id_order='$id'");

    $_SESSION['notif'] = "update";

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!doctype html>
<html>
<head>
<title>Data Order</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

body{
    margin:0;
    background:#ecf0f5;
    font-family:'Segoe UI',sans-serif;
}

/* WRAPPER */
.wrapper{
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */
.sidebar{
    width:250px;
    background:#16a085;
    color:white;
    position:fixed;
    top:0;
    bottom:0;
    left:0;
    padding-top:20px;
}

.sidebar h3{
    text-align:center;
    margin-bottom:20px;
}

.sidebar a{
    display:block;
    padding:12px 20px;
    color:white;
    text-decoration:none;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.15);
}

/* MAIN */
.main-content{
    margin-left:250px;
    width:calc(100% - 250px);
}

/* TOPBAR */
.topbar{
    height:60px;
    background:white;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

/* CONTENT */
.content{
    padding:30px;
}

.foto-jas{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:10px;
}

</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <h3>ADMIN</h3>

        <a href="dashboardadmin.php">
            <i class="fa fa-house"></i> Dashboard
        </a>

        <a href="tb_jas.php">
            <i class="fa fa-shirt"></i> Data Jas
        </a>

        <a href="tb_customer.php">
            <i class="fa fa-user"></i> Customer
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

        <div class="topbar">

            <h5>Data Order</h5>

            <div>
                <?= $_SESSION['username']; ?>
            </div>

        </div>

        <div class="content">

        <!-- NOTIF -->
        <?php if (isset($_SESSION['notif'])): ?>

        <div id="notif" class="alert alert-dismissible fade show 
        <?= $_SESSION['notif']=='tambah'?'alert-success':
        ($_SESSION['notif']=='update'?'alert-warning':
        ($_SESSION['notif']=='hapus'?'alert-danger':'alert-danger')) ?>">

        <?php
        if ($_SESSION['notif']=="tambah") echo "Order berhasil ditambahkan!";
        elseif ($_SESSION['notif']=="update") echo "Order berhasil diupdate!";
        elseif ($_SESSION['notif']=="hapus") echo "Order berhasil dihapus!";
        else echo "Terjadi kesalahan!";
        ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>

        <script>
        setTimeout(function(){
            let notif = document.getElementById('notif');
            if(notif){
                notif.classList.remove('show');
            }
        },3000);
        </script>

        <?php unset($_SESSION['notif']); endif; ?>

        <!-- FORM -->
        <div class="card p-4 mb-4">

        <h5>
            <?= $edit ? "Edit Data Order" : "Tambah Data Order" ?>
        </h5>

        <form method="POST">

        <?php if ($edit): ?>

        <input type="hidden"
        name="id_order"
        value="<?= $e['id_order']; ?>">

        <?php endif; ?>

        <label class="form-label">
            Nama Customer
        </label>

        <select name="id_customer"
        class="form-control mb-3"
        required>

        <option value="">
            -- Pilih Customer --
        </option>

        <?php
        $customer = mysqli_query($conn,"SELECT * FROM tb_customer");

        while ($c = mysqli_fetch_array($customer)) {
        ?>

        <option value="<?= $c['id_customer']; ?>"
        <?= ($edit && $e['id_customer']==$c['id_customer'])?'selected':'' ?>>

        <?= $c['nama_customer']; ?>

        </option>

        <?php } ?>

        </select>

        <label class="form-label">
            Pilih Jas
        </label>

        <select name="id_jas"
        class="form-control mb-3"
        required>

        <option value="">
            -- Pilih Jas --
        </option>

        <?php
        $jas = mysqli_query($conn,"SELECT * FROM tb_jas");

        while ($j = mysqli_fetch_array($jas)) {
        ?>

        <option value="<?= $j['id_jas']; ?>"
        <?= ($edit && $e['id_jas']==$j['id_jas'])?'selected':'' ?>>

        <?= $j['Nama_jas']; ?>
        -
        Rp <?= number_format($j['Harga_sewa']); ?>

        </option>

        <?php } ?>

        </select>

        <label class="form-label">
            Tanggal Sewa
        </label>

        <input type="date"
        name="tanggal_sewa"
        class="form-control mb-3"
        value="<?= $edit?$e['tanggal_sewa']:'' ?>"
        required>

        <label class="form-label">
            Tanggal Kembali
        </label>

        <input type="date"
        name="tanggal_kembali"
        class="form-control mb-3"
        value="<?= $edit?$e['tanggal_kembali']:'' ?>"
        required>

        <label class="form-label">
            Status
        </label>

        <select name="status"
        class="form-control mb-3">

        <option value="diproses"
        <?= ($edit && $e['status']=="diproses")?'selected':'' ?>>
            Diproses
        </option>

        <option value="disewa"
        <?= ($edit && $e['status']=="disewa")?'selected':'' ?>>
            Disewa
        </option>

        <option value="selesai"
        <?= ($edit && $e['status']=="selesai")?'selected':'' ?>>
            Selesai
        </option>

        <option value="dikembalikan"
        <?= ($edit && $e['status']=="dikembalikan")?'selected':'' ?>>
            Dikembalikan
        </option>

        </select>

        <?php if ($edit): ?>

        <button type="submit"
        name="update"
        class="btn btn-warning">

            Update

        </button>

        <a href="?"
        class="btn btn-secondary">

            Batal

        </a>

        <?php else: ?>

        <button type="submit"
        name="simpan"
        class="btn btn-success">

            Simpan

        </button>

        <?php endif; ?>

        </form>
        </div>

        <!-- SEARCH -->
        <div class="card p-3 mb-3">

            <form method="GET" class="d-flex">

                <input type="text"
                name="search"
                class="form-control me-2"
                placeholder="Cari customer / jas / status..."
                value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">

                <button type="submit" class="btn btn-primary">

                    <i class="fa fa-search"></i> Search

                </button>

                <a href="tb_order.php"
                class="btn btn-secondary ms-2">

                    Reset

                </a>

            </form>

        </div>

        <!-- TABEL -->
        <table class="table table-bordered table-striped align-middle">

        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Customer</th>
            <th>Jas</th>
            <th>Sewa</th>
            <th>Kembali</th>
            <th>Status</th>
            <th>Total</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no=1;

        if (isset($_GET['search']) && $_GET['search'] != '') {

            $search = $_GET['search'];

            $data=mysqli_query($conn,"
            SELECT tb_order.*, 
            tb_customer.nama_customer, 
            tb_jas.Nama_jas,
            tb_jas.foto

            FROM tb_order

            JOIN tb_customer
            ON tb_order.id_customer = tb_customer.id_customer

            JOIN tb_jas
            ON tb_order.id_jas = tb_jas.id_jas

            WHERE tb_customer.nama_customer LIKE '%$search%'
            OR tb_jas.Nama_jas LIKE '%$search%'
            OR tb_order.status LIKE '%$search%'
            ");

        } else {

            $data=mysqli_query($conn,"
            SELECT tb_order.*, 
            tb_customer.nama_customer, 
            tb_jas.Nama_jas,
            tb_jas.foto

            FROM tb_order

            JOIN tb_customer
            ON tb_order.id_customer = tb_customer.id_customer

            JOIN tb_jas
            ON tb_order.id_jas = tb_jas.id_jas
            ");

        }

        while($d=mysqli_fetch_array($data)){
        ?>

        <tr>

        <td><?= $no++ ?></td>

        <td>
            <img src="<?= $d['foto']; ?>"
            class="foto-jas">
        </td>

        <td><?= $d['nama_customer'] ?></td>

        <td><?= $d['Nama_jas'] ?></td>

        <td><?= $d['tanggal_sewa'] ?></td>

        <td><?= $d['tanggal_kembali'] ?></td>

        <td><?= $d['status'] ?></td>

        <td>
            Rp <?= number_format($d['total_harga']) ?>
        </td>

        <td>

        <a href="?edit=<?= $d['id_order'] ?>"
        class="btn btn-warning btn-sm">

            Edit

        </a>

        <a href="?hapus=<?= $d['id_order'] ?>"
        class="btn btn-danger btn-sm"
        onclick="return confirm('Yakin hapus data?')">

            Hapus

        </a>

        </td>

        </tr>

        <?php } ?>

        </table>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>