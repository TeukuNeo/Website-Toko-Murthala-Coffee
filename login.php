<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('location:admin.php');
    } else {
        header("location:index.php");
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary" style="display: flex; align-items:center; justify-content: center; min-height: 100vh; background-color: #f8f9fa;">
    <div class="container">
        <main class="form-signin w-100 m-auto" style="max-width: 400px; width: 100%; padding: 20px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); border-radius: 0.5rem; background-color: white;">
            <form action="prosesLogin.php" method="POST">
                <h1 class="h3 mb-3 fw-normal text-center">Silahkan Masuk</h1>
                <div class="form-floating">
                    <input type="text" name="username" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">Username</label>
                </div>
                <div class="form-floating my-3">
                    <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                </div>
                <button class="btn btn-primary w-100 py-2 mb-3" name="submit_login" type="submit">Sign in</button>
                <a class="btn btn-outline-secondary w-100 py-2 mb-3" href="registrasi.php">Sign Up</a>
                <p class="mt-5 mb-3 text-body-secondary text-center">© 2025 Murthala, Coffee</p>
            </form>
        </main>
    </div>
</body>
<script src="js/bootstrap.bundle.min.js"></script>

</html>