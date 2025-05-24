<?php

require 'function.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $cekdatabase = mysqli_query($conn, "SELECT * FROM login WHERE email='$email' AND password='$password'");
    $hitung = mysqli_num_rows($cekdatabase);

    if ($hitung > 0) {
        $_SESSION['log'] = true;
        header('Location: index.php');
        exit;
    } else {
        header('Location: login.php');
        exit;
    }
}

// Mengecek apakah sudah login, kalau iya redirect
if (isset($_SESSION['log'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <style>
        body {
    height: 100vh;
    background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    margin: 0;
    padding: 0;
}

body::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(30, 50, 30, 0.35); /* overlay hijau gelap agak transparan */
    z-index: 0;
}

/* Container Form Login */
.login-container {
    width: 100%;
    max-width: 420px;
    padding: 20px;
    position: relative;
    z-index: 1;
}

/* Kartu Login */
.card {
    background: rgba(56, 82, 43, 0.6); /* hijau alami transparan */
    backdrop-filter: blur(15px);
    border-radius: 20px;
    border: 1px solid rgba(98, 130, 66, 0.5);
    box-shadow: 0 10px 30px rgba(56, 82, 43, 0.6);
    overflow: hidden;
    color: #e8f0d6; /* warna teks terang ke kuning alami */
    width: 100%;
}

/* Header */
.card-header {
    background: linear-gradient(to right, #a2c18e, #789262);
    padding: 20px;
    text-align: center;
    font-weight: bold;
    font-size: 1.3rem;
    color: #f1f7e7;
    user-select: none;
}

/* Body */
.card-body {
    padding: 30px 25px;
}

/* Input Fields */
.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 6px;
    color: #d9e6be; /* warna label senada */
    font-weight: 600;
}

input[type="email"],
input[type="password"] {
    width: 100%; /* pastikan tidak melebihi */
    max-width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1.5px solid rgba(217, 230, 190, 0.7);
    background-color: rgba(255, 255, 255, 0.15);
    color: #e8f0d6;
    font-size: 1rem;
    transition: background 0.3s ease, border-color 0.3s ease;
    box-sizing: border-box;
}

input::placeholder {
    color: rgba(232, 240, 214, 0.6);
}

input:focus {
    outline: none;
    border-color: #c2d59e;
    background-color: rgba(255, 255, 255, 0.3);
}

/* Tombol Login */
.btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(to right, #a2c18e, #789262);
    border: none;
    border-radius: 10px;
    color: #2b3a12;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(122, 159, 81, 0.6);
}

.btn:hover {
    transform: scale(1.05);
    background: linear-gradient(to left, #a2c18e, #789262);
}

    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <h2>Login</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="form-group">
                        <label for="inputEmail">Email</label>
                        <input type="email" id="inputEmail" name="email" required placeholder="name@example.com" />
                    </div>
                    <div class="form-group">
                        <label for="inputPassword">Password</label>
                        <input type="password" id="inputPassword" name="password" required placeholder="Password" />
                    </div>
                    <button type="submit" name="login" class="btn">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
