<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Agrega esto en <head> para usar Inter y los estilos -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Empregos Board</a>
            <div class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['is_admin']): ?>
                        <a class="nav-link" href="admin/jobs.php">Gerenciar Vagas</a>
                        <a class="nav-link" href="admin/categories.php">Gerenciar Categorias</a>
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php">Sair</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Cadastrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>