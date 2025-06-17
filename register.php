<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $linkedin = $_POST['linkedin'];

    // Upload da foto
    $photo = '';
    if ($_FILES['photo']['name']) {
        $target_dir = "uploads/";
        $photo_name = time() . '_' . basename($_FILES['photo']['name']);
        $photo = $target_dir . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, photo, linkedin, is_admin) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->execute([$name, $email, $password, $photo, $linkedin]);

    // O ideal é redirecionar para uma página de login também em espanhol, se houver.
    header("Location: login.php"); 
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea tu Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #D9534F;
            --light-gray-bg: #F8F9FA;
            --dark-text: #333;
            --border-color: #DEE2E6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .signup-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .image-section {
            flex-basis: 50%;
            background: url('https://images.pexels.com/photos/3769021/pexels-photo-3769021.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1') no-repeat center center;
            background-size: cover;
        }

        .form-section {
            flex-basis: 50%;
            padding: 3rem;
        }

        .form-section .subtitle {
            color: var(--primary-color);
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .form-section h2 {
            color: var(--dark-text);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(217, 83, 79, 0.25);
        }

        .btn-custom {
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            width: 100%;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #c9302c;
        }
        
        .login-link {
            font-size: 0.9rem;
            margin-top: 1rem;
            text-align: center;
        }

        @media (max-width: 992px) {
            .signup-container {
                flex-direction: column;
                margin: 1rem;
            }
            .image-section {
                display: none; 
            }
            .form-section {
                flex-basis: 100%;
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="image-section">
        </div>
        <div class="form-section">
            <p class="subtitle">Começe agora</p>
            <h2>Crie sua conta</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Tu nombre completo">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="tu@email.com">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contrasenha</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Crea una contraseña segura">
                </div>
                <div class="mb-3">
                    <label for="linkedin" class="form-label">Perfil do LinkedIn (Opcional)</label>
                    <input type="url" class="form-control" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/tu-perfil">
                </div>
                 <div class="mb-3">
                    <label for="photo" class="form-label">Foto de Perfil (Opcional)</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Criar Perfil</button>
            </form>
            <div class="login-link">
                <p>ja tem uma conta? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>