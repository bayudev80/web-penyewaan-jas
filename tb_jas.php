<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "sewajas");

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// BUAT FOLDER FOTO JIKA BELUM ADA
if (!file_exists("foto_jas")) {
    mkdir("foto_jas");
}

// TAMBAH DATA
if (isset($_POST['simpan'])) {

    $nama   = $_POST['Nama_jas'];
    $ukuran = $_POST['Ukuran_jas'];
    $warna  = $_POST['Warna_jas'];
    $harga  = $_POST['Harga_sewa'];
    $stok   = $_POST['Stok'];

    // FOTO
    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    move_uploaded_file($tmp, "foto_jas/" . $foto);

    mysqli_query($conn, "INSERT INTO tb_jas 
        (Nama_jas, Ukuran_jas, Warna_jas, Harga_sewa, Stok, foto) 
        VALUES ('$nama','$ukuran','$warna','$harga','$stok','$foto')");

    $_SESSION['notif'] = "tambah";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// HAPUS
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $ambil = mysqli_query($conn, "SELECT * FROM tb_jas WHERE id_jas='$id'");
    $dataHapus = mysqli_fetch_array($ambil);

    if ($dataHapus['foto'] != '') {
        unlink("foto_jas/" . $dataHapus['foto']);
    }

    mysqli_query($conn, "DELETE FROM tb_jas WHERE id_jas='$id'");

    $_SESSION['notif'] = "hapus";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// AMBIL DATA EDIT
$edit = false;

if (isset($_GET['edit'])) {
    $edit = true;

    $id = $_GET['edit'];

    $data_edit = mysqli_query($conn, "SELECT * FROM tb_jas WHERE id_jas='$id'");
    $e = mysqli_fetch_array($data_edit);
}

// UPDATE
if (isset($_POST['update'])) {

    $id     = $_POST['id_jas'];
    $nama   = $_POST['Nama_jas'];
    $ukuran = $_POST['Ukuran_jas'];
    $warna  = $_POST['Warna_jas'];
    $harga  = $_POST['Harga_sewa'];
    $stok   = $_POST['Stok'];

    // FOTO BARU
    if ($_FILES['foto']['name'] != '') {

        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        move_uploaded_file($tmp, "foto_jas/" . $foto);

        mysqli_query($conn, "UPDATE tb_jas SET
            Nama_jas='$nama',
            Ukuran_jas='$ukuran',
            Warna_jas='$warna',
            Harga_sewa='$harga',
            Stok='$stok',
            foto='$foto'
            WHERE id_jas='$id'");

    } else {

        mysqli_query($conn, "UPDATE tb_jas SET
            Nama_jas='$nama',
            Ukuran_jas='$ukuran',
            Warna_jas='$warna',
            Harga_sewa='$harga',
            Stok='$stok'
            WHERE id_jas='$id'");
    }

    $_SESSION['notif'] = "update";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Data Jas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
    margin: 0;
    background: #ecf0f5;
    font-family: 'Segoe UI', sans-serif;
}

.wrapper {
    display: flex;
    min-height: 100vh;
}

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

.main-content {
    margin-left: 250px;
    width: calc(100% - 250px);
}

.topbar {
    height: 60px;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.content {
    padding: 30px;
}

img {
    border-radius: 10px;
}
</style>
</head>

<body>

<div class="wrapper">

<div class="sidebar">
    <h3>ADMIN</h3>
    <a href="dashboardadmin.php"><i class="fa fa-house"></i> Dashboard</a>
    <a href="tb_jas.php"><i class="fa fa-shirt"></i> Data Jas</a>
    <a href="tb_customer.php"><i class="fa fa-user"></i> Customer</a>
    <a href="tb_order.php"><i class="fa fa-cart-shopping"></i> Order</a>
    <a href="logout.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">

<div class="topbar">
    <h5>Data Jas</h5>

    <div>
        <?= $_SESSION['username']; ?>
    </div>
</div>

<div class="content">

<!-- FORM -->
<div class="card p-4 mb-4">

<form method="POST" enctype="multipart/form-data">

<?php if ($edit) { ?>
<input type="hidden" name="id_jas" value="<?= $e['id_jas']; ?>">
<?php } ?>

<input type="text" name="Nama_jas" class="form-control mb-2"
value="<?= $edit ? $e['Nama_jas'] : '' ?>" placeholder="Nama Jas" required>

<input type="text" name="Ukuran_jas" class="form-control mb-2"
value="<?= $edit ? $e['Ukuran_jas'] : '' ?>" placeholder="Ukuran" required>

<input type="text" name="Warna_jas" class="form-control mb-2"
value="<?= $edit ? $e['Warna_jas'] : '' ?>" placeholder="Warna" required>

<input type="number" name="Harga_sewa" class="form-control mb-2"
value="<?= $edit ? $e['Harga_sewa'] : '' ?>" placeholder="Harga" required>

<input type="number" name="Stok" class="form-control mb-2"
value="<?= $edit ? $e['Stok'] : '' ?>" placeholder="Stok" required>

<input type="file" name="foto" class="form-control mb-3">

<?php if ($edit && $e['foto'] != '') { ?>
<img src="foto_jas/<?= $e['foto']; ?>" width="120" class="mb-3">
<?php } ?>

<?php if ($edit) { ?>
<button type="submit" name="update" class="btn btn-warning">Update</button>
<a href="?" class="btn btn-secondary">Batal</a>
<?php } else { ?>
<button type="submit" name="simpan" class="btn btn-success">Simpan</button>
<?php } ?>

</form>
</div>

<!-- TABEL -->
<table class="table table-bordered">

<tr>
<th>No</th>
<th>Foto</th>
<th>Nama</th>
<th>Ukuran</th>
<th>Warna</th>
<th>Harga</th>
<th>Stok</th>
<th>Aksi</th>
</tr>

<?php
$no = 1;

if (isset($_GET['search']) && $_GET['search'] != '') {

    $search = $_GET['search'];

    $data = mysqli_query($conn, "SELECT * FROM tb_jas 
        WHERE Nama_jas LIKE '%$search%'
        OR Ukuran_jas LIKE '%$search%'
        OR Warna_jas LIKE '%$search%'");

} else {

    $data = mysqli_query($conn, "SELECT * FROM tb_jas");
}

while ($d = mysqli_fetch_array($data)) {
?>

<tr>

<td><?= $no++; ?></td>

<td>
<?php if ($d['foto'] != '') { ?>
<img src="foto_jas/<?= $d['foto']; ?>" width="80">
<?php } ?>
</td>

<td><?= $d['Nama_jas']; ?></td>
<td><?= $d['Ukuran_jas']; ?></td>
<td><?= $d['Warna_jas']; ?></td>
<td><?= $d['Harga_sewa']; ?></td>
<td><?= $d['Stok']; ?></td>

<td>
<a href="?edit=<?= $d['id_jas']; ?>" class="btn btn-sm btn-warning">Edit</a>

<a href="?hapus=<?= $d['id_jas']; ?>" 
class="btn btn-sm btn-danger"
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

</body>
</html>