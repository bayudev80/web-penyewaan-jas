<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "user");

// LOGIN
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM tb_user WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $_SESSION['username'] = $username;
        header("Location: dashboardadmin.php");
        exit();
    } else {
        echo "<script>alert('Login gagal!');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #4e73df, #1cc88a);
      height: 100vh;
    }

    .login-card {
      background: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .login-title {
      font-weight: 600;
      color: #4e73df;
    }

    .btn-primary {
      background: #4e73df;
      border: none;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background: #2e59d9;
    }

    .form-control {
      border-radius: 10px;
    }
  </style>
</head>

<body>

<div class="d-flex justify-content-center align-items-center vh-100">
  <div class="login-card col-md-4">

    <form method="POST">
      
      <h2 class="text-center mb-4 login-title">Welcome</h2>

      <div class="form-floating mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
        <label>Username</label>
      </div>

      <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <label>Password</label>
      </div>

      <button class="w-100 btn btn-primary py-2" name="login" type="submit">
        Login
      </button>

      <p class="text-center mt-3 text-muted" style="font-size: 14px;">
      </p>

    </form>

  </div>
</div>

</body>
</html>