<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - SMK Tunas Harapan Pati</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
        }

        .top-header {
            background-color: #c0c0c0;
            padding: 25px 30px;
            text-align: left;
            width: 100%;
            border-bottom: 2px solid #999;
        }

        .top-header h1 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .top-header p {
            font-size: 13px;
            color: #555;
            margin: 3px 0 0 0;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        .header {
            background-color: #c0c0c0;
            padding: 30px;
            text-align: center;
            margin-bottom: 0;
            border-radius: 8px 8px 0 0;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #555;
            margin: 2px 0;
        }

        .form-container {
            background-color: #d3d3d3;
            padding: 50px 40px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 15px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            text-decoration: underline;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            font-size: 14px;
            border: 1px solid #999;
            border-radius: 4px;
            background-color: #fff;
            color: #333;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: #333;
            background-color: #e8e8e8;
            border: 1px solid #999;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-register:hover {
            background-color: #d0d0d0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-register:active {
            transform: translateY(1px);
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 30px 20px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 16px;
            }

            .header p {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="top-header" style="display: flex; align-items: center; gap: 20px;">
        <h1>LOGO SMK THP</h1>
        <div style="display: flex; align-items: center; gap: 20px;">
            <p>SMK Tunas Harapan Pati</p>
            <p>SMK Bisa SMK Hebat</p>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
            <form method="POST" action="/register">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-register">Create Account</button>
            </form>
            </div>
        </div>
    </div>
</body>
</html>
