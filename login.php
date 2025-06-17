<?php
session_start();
require_once 'includes/db.php';

// Redireciona se o usuário já estiver logado
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: " . ($user['is_admin'] ? "admin/jobs.php" : "index.php"));
        exit;
    } else {
        // Mensagem de erro traduzida para o espanhol
        $error = "Credenciais inválidas. Por favor, tente novamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .auth-container {
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
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
            border-radius: 8px;
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
        
        .register-link {
            font-size: 0.9rem;
            margin-top: 1rem;
            text-align: center;
        }

        @media (max-width: 992px) {
            .auth-container {
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
    <div class="auth-container">
        <div class="image-section">
            </div>
        <div class="form-section">
            <p class="subtitle">¡BemVindo!</p>
            <h2>login</h2>
            
            <?php if (isset($error)): ?>
                <div class='alert alert-danger'><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Ingresa tu correo electrónico">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Ingresa tu contraseña">
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Ingresar</button>
            </form>
            <div class="register-link">
                <p>Não tem uma conta? <a href="register.php">Regístrese</a></p>
            </div>
        </div>
    </div>
</body>
</html>