<?php
session_start();
require_once '../includes/db.php';

// 1. Verifica se o usuário é admin e está logado
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit;
}

// 2. Valida se um ID de vaga foi passado pela URL
if (!isset($_GET['job_id'])) {
    header("Location: jobs.php");
    exit;
}

$job_id = $_GET['job_id'];

// 3. Busca o título da vaga CORRETAMENTE
$stmt_job = $pdo->prepare("SELECT title FROM jobs WHERE id = ?");
$stmt_job->execute([$job_id]);
$job = $stmt_job->fetch(); 

// Se a vaga não for encontrada, redireciona de volta
if (!$job) {
    header("Location: jobs.php");
    exit;
}

// 4. Busca os usuários que se aplicaram para esta vaga
$stmt_apps = $pdo->prepare("SELECT u.name, u.email, u.photo, u.linkedin FROM applications a JOIN users u ON a.user_id = u.id WHERE a.job_id = ?");
$stmt_apps->execute([$job_id]);
$applications = $stmt_apps->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Postulantes de la Vacante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .section-box {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .section-box h3 {
            margin-bottom: 1.5rem;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }
        .page-header { text-align: center; padding: 2rem 0; }
        .page-header h2 { font-size: 2.8rem; font-weight: 300; color: #333; }
        .page-header p { font-size: 1.1rem; color: #6c757d; max-width: 600px; margin: 0 auto; }
        .profile-photo { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Panel de Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="jobs.php">Vacantes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categorías</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="page-header">
            <h2>Postulantes de la Vacante</h2>
            <p class="text-muted fs-4"><?php echo htmlspecialchars($job['title']); ?></p>
        </div>

        <div class="section-box">
            <div class="d-flex justify-content-between align-items-center mb-3">
                 <h3>Lista de Candidatos</h3>
                 <a href="jobs.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver a Vacantes</a>
            </div>
           
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th class="text-center">LinkedIn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php // Ponto crítico: Verifique se este bloco está completo no seu arquivo ?>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No hay postulantes para esta vacante todavía.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo htmlspecialchars($app['photo'] ?: 'uploads/default.jpg'); ?>" alt="Foto de perfil" class="profile-photo">
                                    </td>
                                    <td><?php echo htmlspecialchars($app['name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['email']); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo htmlspecialchars($app['linkedin']); ?>" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fab fa-linkedin"></i> Ver Perfil
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; // Fechamento do FOREACH ?>
                        <?php endif; // Fechamento do IF/ELSE ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>