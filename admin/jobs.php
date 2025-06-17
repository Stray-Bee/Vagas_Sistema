
<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $contact = $_POST['contact'];
        $category_id = $_POST['category_id'];
        $image = '';
        if ($_FILES['image']['name']) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $image = $target_dir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image);
        }
        $stmt = $pdo->prepare("INSERT INTO jobs (title, description, contact, image, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $contact, $image, $category_id]);
    } elseif (isset($_POST['toggle'])) {
        $id = $_POST['id'];
        $is_active = $_POST['is_active'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE jobs SET is_active = ? WHERE id = ?");
        $stmt->execute([$is_active, $id]);
    }
    header("Location: jobs.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$jobs = $pdo->query("SELECT j.*, c.name as category_name FROM jobs j JOIN categories c ON j.category_id = c.id ORDER BY j.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Vacantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            text-align: center;
            padding: 3rem 0;
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
        .steps-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin: 2rem 0 4rem 0;
            position: relative;
        }
        .step {
            text-align: center;
            flex: 1;
            max-width: 280px;
            padding: 0 15px;
            position: relative;
            z-index: 1;
        }
        .step-number {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            font-size: 1.75rem;
            font-weight: 700;
            color: #e63946;
        }
        .step h4 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #343a40;
        }
        .step p {
            font-size: 0.95rem;
            color: #6c757d;
        }
        .steps-container::before {
            content: '';
            position: absolute;
            top: 30px;
            left: 20%;
            right: 20%;
            height: 2px;
            background-image: linear-gradient(to right, #ced4da 50%, transparent 50%);
            background-size: 20px 2px;
            background-repeat: repeat-x;
            z-index: 0;
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
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Painel do Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Inicio</a>
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
            <h2>Pra Empresas</h2>
            <p>Com apenas alguns passos simples, você encontrará os candidatos ideais que procura!</p>
        </div>

        <div class="steps-container">
            <div class="step">
                <div class="step-number">1</div>
                <h4>Crie a Vacante</h4>
                <p>Cadastre-se agora e descubra as melhores oportunidades profissionais.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h4>Administre as Vacantes</h4>
                <p>Descubra profissionais altamente capacitados e selecione o melhor talento para sua equipe.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h4>Encontre o Candidato Ideal</h4>
                <p>Use nossos filtros avançados e encontre o profissional perfeito para sua oportunidade.</p>
            </div>
        </div>

        <div class="section-box">
            <h3>Criar Nova Vacante</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Título da Vacante</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Descrição Completa</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contact" class="form-label">Contato (E-mail o Telefone)</label>
                        <input type="text" class="form-control" id="contact" name="contact" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Categoría</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Imagen (Opcional)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>
                <button type="submit" name="add" class="btn btn-primary px-4">Criar Vacante</button>
            </form>
        </div>
        
        <div class="section-box">
            <h3>Administrar Vacantes Existentes</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                <td><?php echo htmlspecialchars($job['category_name']); ?></td>
                                <td class="text-center">
                                    <?php if ($job['is_active']): ?>
                                        <span class="badge bg-success">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form method="POST" class="d-inline-block me-2">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($job['id']); ?>">
                                        <input type="hidden" name="is_active" value="<?php echo htmlspecialchars($job['is_active']); ?>">
                                        <button type="submit" name="toggle" class="btn btn-sm <?php echo $job['is_active'] ? 'btn-danger' : 'btn-success'; ?>">
                                            <?php echo $job['is_active'] ? 'Desactivar' : 'Activar'; ?>
                                        </button>
                                    </form>
                                    <a href="view_applications.php?job_id=<?php echo htmlspecialchars($job['id']); ?>" class="btn btn-sm btn-info">Visualizar Candidatos</a>
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
```