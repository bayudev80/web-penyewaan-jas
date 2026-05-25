<?php
$conn = mysqli_connect("localhost", "root", "", "sewajas");

/* ================= HITUNG TOTAL ================= */
function hitungTotal($conn, $id_jas, $tgl_sewa, $tgl_kembali){

    $q = mysqli_query($conn, "SELECT Harga_sewa FROM tb_jas WHERE id_jas='$id_jas'");
    $j = mysqli_fetch_array($q);

    $harga = $j['Harga_sewa'];

    $lama = floor((strtotime($tgl_kembali) - strtotime($tgl_sewa)) / 86400) + 1;

    if($lama < 1){
        $lama = 1;
    }

    $denda = 0;

    if($lama > 3){
        $denda = ($lama - 3) * 50000;
    }

    return $harga + $denda;
}

/* ================= SIMPAN ================= */
if(isset($_POST['sewa'])){

    $nama = $_POST['nama_customer'];
    $hp = $_POST['no_hp'];
    $ktp = $_POST['KTP'];

    $id_jas = $_POST['id_jas'];

    $tanggal_sewa = $_POST['tanggal_sewa'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // simpan customer
    mysqli_query($conn,"INSERT INTO tb_customer
    (nama_customer,no_hp,KTP)
    VALUES
    ('$nama','$hp','$ktp')");

    $id_customer = mysqli_insert_id($conn);

    // hitung total
    $total = hitungTotal($conn,$id_jas,$tanggal_sewa,$tanggal_kembali);

    // simpan order
    mysqli_query($conn,"INSERT INTO tb_order
    (id_customer,id_jas,tanggal_sewa,tanggal_kembali,status,total_harga)
    VALUES
    ('$id_customer','$id_jas','$tanggal_sewa','$tanggal_kembali','diproses','$total')");

    // kurangi stok
    mysqli_query($conn,"UPDATE tb_jas
    SET Stok = Stok - 1
    WHERE id_jas='$id_jas'");

    echo "
    <script>
        alert('Penyewaan berhasil!');
        window.location='sewa.php';
    </script>
    ";
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Sewa Jas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:'Segoe UI',sans-serif;
}

.navbar{
    background:#16a085;
    padding:15px;
}

.navbar h2{
    color:white;
    margin:0;
    font-weight:bold;
}

.produk{
    background:white;
    border-radius:15px;
    overflow:hidden;
    transition:0.3s;
    height:100%;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.produk:hover{
    transform:translateY(-5px);
}

.produk img{
    width:100%;
    height:260px;
    object-fit:cover;
}

.produk-body{
    padding:15px;
}

.harga{
    color:#16a085;
    font-size:22px;
    font-weight:bold;
}

.btn-sewa{
    background:#16a085;
    color:white;
    width:100%;
    border:none;
}

.btn-sewa:hover{
    background:#13856d;
    color:white;
}

.modal-header{
    background:#16a085;
    color:white;
}

</style>

</head>
<body>

<div class="navbar">
    <h2>SEWA JAS</h2>
</div>

<div class="container mt-4">

    <!-- SEARCH -->
    <div class="row mb-4">

        <div class="col-md-6">

            <form method="GET" class="d-flex">

                <input type="text"
                name="search"
                class="form-control me-2"
                placeholder="Cari nama jas / ukuran / warna..."
                value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">

                <button type="submit"
                class="btn btn-success">

                    Search

                </button>

                <a href="sewa.php"
                class="btn btn-secondary ms-2">

                    Reset

                </a>

            </form>

        </div>

    </div>

    <div class="row">

        <?php

        // SEARCH
        if(isset($_GET['search']) && $_GET['search'] != ''){

            $search = $_GET['search'];

            $data = mysqli_query($conn,"
            SELECT * FROM tb_jas
            WHERE Stok > 0
            AND (
                Nama_jas LIKE '%$search%'
                OR Ukuran_jas LIKE '%$search%'
                OR Warna_jas LIKE '%$search%'
            )
            ");

        }else{

            $data = mysqli_query($conn,"
            SELECT * FROM tb_jas
            WHERE Stok > 0
            ");
        }

        while($j = mysqli_fetch_array($data)){
        ?>

        <div class="col-md-3 mb-4">

            <div class="produk">

                <!-- FOTO JAS -->
                <img src="<?= $j['foto']; ?>">

                <div class="produk-body">

                    <h5><?= $j['Nama_jas']; ?></h5>

                    <p>
                        Ukuran : <?= $j['Ukuran_jas']; ?>
                        <br>
                        Warna : <?= $j['Warna_jas']; ?>
                    </p>

                    <div class="harga">
                        Rp <?= number_format($j['Harga_sewa']); ?>
                    </div>

                    <p class="mt-2">
                        Stok : <?= $j['Stok']; ?>
                    </p>

                    <button
                    class="btn btn-sewa mt-2"
                    data-bs-toggle="modal"
                    data-bs-target="#modal<?= $j['id_jas']; ?>">

                        Sewa Sekarang

                    </button>

                </div>

            </div>

        </div>

        <!-- MODAL -->
        <div class="modal fade" id="modal<?= $j['id_jas']; ?>">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5>Checkout Penyewaan</h5>

                        <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-5">

                                <!-- FOTO BESAR -->
                                <img src="<?= $j['foto']; ?>"
                                class="img-fluid rounded">

                            </div>

                            <div class="col-md-7">

                                <h3><?= $j['Nama_jas']; ?></h3>

                                <p>
                                    Ukuran :
                                    <?= $j['Ukuran_jas']; ?>
                                </p>

                                <p>
                                    Warna :
                                    <?= $j['Warna_jas']; ?>
                                </p>

                                <h4 class="text-success">
                                    Rp <?= number_format($j['Harga_sewa']); ?>
                                </h4>

                                <hr>

                                <form method="POST">

                                    <input type="hidden"
                                    name="id_jas"
                                    value="<?= $j['id_jas']; ?>">

                                    <div class="mb-2">

                                        <label>Nama Customer</label>

                                        <input type="text"
                                        name="nama_customer"
                                        class="form-control"
                                        required>

                                    </div>

                                    <div class="mb-2">

                                        <label>No HP</label>

                                        <input type="text"
                                        name="no_hp"
                                        class="form-control"
                                        required>

                                    </div>

                                    <div class="mb-2">

                                        <label>No KTP</label>

                                        <input type="text"
                                        name="KTP"
                                        class="form-control"
                                        required>

                                    </div>

                                    <div class="mb-2">

                                        <label>Tanggal Sewa</label>

                                        <input type="date"
                                        name="tanggal_sewa"
                                        class="form-control"
                                        required>

                                    </div>

                                    <div class="mb-3">

                                        <label>Tanggal Kembali</label>

                                        <input type="date"
                                        name="tanggal_kembali"
                                        class="form-control"
                                        required>

                                    </div>

                                    <button type="submit"
                                    name="sewa"
                                    class="btn btn-success w-100">

                                        Konfirmasi Sewa

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>