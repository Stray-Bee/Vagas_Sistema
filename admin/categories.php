<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: categories.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
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
        .btn-primary {
             background-color: #e63946;
             border-color: #e63946;
        }
        .btn-primary:hover {
            background-color: #d62828;
            border-color: #d62828;
        }
        .page-header {
            text-align: center;
            padding: 2rem 0;
        }
        .page-header h2 {
            font-size: 2.8rem;
            font-weight: 300;
            color: #333;
        }
        .page-header p {
            font-size: 1.1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Panel de Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="categories.php">Categorías</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">

        <div class="page-header">
            <h2>Administración de Categorías</h2>
            <p>Añada, edite o elimine las categorías que se usarán para clasificar las vacantes de empleo.</p>
        </div>

        <div class="section-box">
            <h3>Añadir Nueva Categoría</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre de la Categoría</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Ej: Tecnología, Ventas, Diseño">
                </div>
                <button type="submit" name="add" class="btn btn-primary"><i class="fas fa-plus"></i> Añadir Categoría</button>
            </form>
        </div>

        <div class="section-box">
            <h3>Categorías Existentes</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 10%;">ID</th>
                            <th scope="col">Nombre</th>
                            <th scope="col" class="text-center" style="width: 45%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <th scope="row"><?php echo htmlspecialchars($category['id']); ?></th>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <form method="POST" class="d-inline-block me-2 flex-grow-1" style="max-width: 300px;">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id']); ?>">
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                                                <button type="submit" name="edit" class="btn btn-sm btn-warning"><i class="fas fa-pencil-alt"></i> Actualizar</button>
                                            </div>
                                        </form>
                                        <form method="POST" class="d-inline-block">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id']); ?>">
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta categoría?');">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>