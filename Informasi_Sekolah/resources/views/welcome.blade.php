<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMK Tunas Harapan Pati</title>
    <style>
        /* Gaya untuk Background Luar */
        body {
            background-color: #f0f0f0; /* Abu-abu sangat muda */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        /* Kotak Abu-abu Tengah (Sesuai Gambar 2) */
        .login-box {
            background-color: #d9d9d9; /* Warna abu-abu kotak */
            padding: 40px;
            width: 450px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 30px;
            font-weight: normal;
        }

        .input-group {
            text-align: left;
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 18px;
            margin-bottom: 5px;
        }

        /* Gaya Input */
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #7a7a7a;
            box-sizing: border-box; /* Agar padding tidak merusak lebar */
            font-size: 16px;
        }

        /* Gaya Tombol Log In */
        button {
            width: 100%;
            padding: 12px;
            background-color: white;
            border: 1px solid #7a7a7a;
            font-size: 20px;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
        }

        button:hover {
            background-color: #e6e6e6;
        }

        .register-link {
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>Web Informasi SMK Tunas Harapan Pati</h2>

        <form action="/login" method="POST">
            @csrf <div class="input-group">
                <label>Username or Email</label>
                <input type="text" name="email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Log In</button>
        </form>

        <div class="register-link">
            Need an account? <a href="#" style="color: #007bff; text-decoration: none;">Register</a>
        </div>
    </div>

</body>
</html>
