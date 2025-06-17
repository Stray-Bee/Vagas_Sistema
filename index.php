<?php
session_start(); // Inicia la sesión para acceder a los datos del usuario (como user_id).
require_once 'includes/db.php'; // Incluye la conexión a la base de datos usando PDO.

// Si el usuario está logueado, buscamos su nombre en la base de datos.
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt_user = $pdo->prepare("SELECT name FROM users WHERE id = ?");
    $stmt_user->execute([$_SESSION['user_id']]);
    $user = $stmt_user->fetch(); // Almacena el nombre del usuario en $user.
}

// Obtenemos los IDs de las ofertas a las que el usuario ya se postuló.
$applied_job_ids = [];
if (isset($_SESSION['user_id'])) {
    $stmt_applied = $pdo->prepare("SELECT job_id FROM applications WHERE user_id = ?");
    $stmt_applied->execute([$_SESSION['user_id']]);
    // Creamos un array simple con los IDs de las vacantes: ej. [1, 2, 5]
    $applied_job_ids = $stmt_applied->fetchAll(PDO::FETCH_COLUMN, 0);
}

// Parámetros de filtro desde la URL: categoría y búsqueda textual
$category_id = $_GET['category_id'] ?? ''; // ID de la categoría
$search_query = $_GET['search'] ?? ''; // Término de búsqueda

$where_clauses = ["j.is_active = 1"]; // Solo vacantes activas
$params = []; // Array para los parámetros de PDO

// Si hay categoría, añadimos al WHERE
if ($category_id) {
    $where_clauses[] = "j.category_id = :category_id";
    $params[':category_id'] = $category_id;
}

// Si hay texto de búsqueda, añadimos condiciones al WHERE
if ($search_query) {
    $where_clauses[] = "(j.title LIKE :search_query OR j.description LIKE :search_query)";
    $params[':search_query'] = '%' . $search_query . '%';
}

// Convertimos el array de condiciones en una cadena SQL
$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Consulta principal que obtiene las vacantes y el nombre de su categoría
$stmt = $pdo->prepare("SELECT j.*, c.name as category_name FROM jobs j JOIN categories c ON j.category_id = c.id $where_sql");
$stmt->execute($params);
$jobs = $stmt->fetchAll(); // Guardamos todas las vacantes encontradas

// Consulta para obtener todas las categorías (para menús, filtros, etc.)
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Si el usuario hace clic en "Candidatar-se", insertamos una postulación
if (isset($_POST['apply']) && isset($_SESSION['user_id'])) {
    $job_id = $_POST['job_id'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("INSERT INTO applications (user_id, job_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $job_id]);

    // Redireccionamos para evitar reenvíos de formulario
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Metadatos del documento -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crie Oportunidades, Descubra Carreiras</title>

    <!-- Estilos externos y tipografías -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Estilos internos personalizados -->
    <style>
        .nav-actions { display: flex; align-items: center; gap: 15px; }
        .user-greeting { font-weight: 500; color: #333; }
        .nav-links li a { padding: 10px 15px; }
        .btn-success:disabled {
            background-color: #198754;
            border-color: #198754;
            opacity: 0.7;
        }

        /* Estilo para tarjetas de vacantes con imagen */
        .job-card {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .job-card-image {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            background-color: #f0f0f0;
        }
        .job-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .job-card-content {
            flex-grow: 1;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header class="main-header">
        <div class="container">
            <nav class="main-nav">
                <!-- Menú principal -->
                <ul class="nav-links">
                    <li><a href="#" class="active">Início</a></li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="admin/jobs.php">Painel</a></li>
                    <?php endif; ?>
                </ul>

                <!-- Ações do usuário -->
                <div class="nav-actions">
                    <?php if ($user): ?>
                        <div class="user-greeting">Olá, <?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?></div>
                        <a href="logout.php" class="btn btn-secondary">Sair</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-secondary">Registrarse</a>
                        <a href="login.php" class="btn btn-primary">Iniciar sesión</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main>

        <!-- Hero / Banner -->
        <section class="hero-section">
            <div class="container hero-container">
                <div class="hero-content">
                    <h1>Crear oportunidades,<br>Descubre carreras</h1>
                    <p>Donde los objetivos y aspiraciones se consolidan cada mes, con trayectorias profesionales construidas día a día.</p>
                </div>
            </div>
        </section>

        <!-- Campo de busca -->
        <section class="search-section">
            <div class="container">
                <form action="index.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Encontre uma Vaga..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Pesquisar</button>
                </form>
            </div>
        </section>

        <!-- Lista de vagas -->
        <section class="jobs-section">
            <div class="container">
                <div class="job-listings">
                    <?php if (empty($jobs)): ?>
                        <p class="no-jobs-found">Nenhuma vaga encontrada. Tente uma busca diferente.</p>
                    <?php else: ?>
                        <?php foreach ($jobs as $job): ?>
                            <div class="job-card">
                                <!-- Imagem da vaga -->
                                <div class="job-card-image">
                                    <?php
                                        $default_image = 'assets/images/default-placeholder.png'; 
                                        $image_path = !empty($job['image']) ? str_replace('../', '', $job['image']) : $default_image;
                                        if (!file_exists($image_path)) {
                                            $image_path = $default_image;
                                        }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Logo da vaga">
                                </div>

                                <!-- Conteúdo textual -->
                                <div class="job-card-content">
                                    <div class="job-card-header">
                                        <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                                        <span class="job-category"><?php echo htmlspecialchars($job['category_name']); ?></span>
                                    </div>
                                    <p class="job-description"><?php echo substr(htmlspecialchars($job['description']), 0, 120); ?>...</p>
                                    <div class="job-card-footer">
                                        <span class="job-contact"><strong>Contato:</strong> <?php echo htmlspecialchars($job['contact']); ?></span>
                                        
                                        <!-- Botão de candidatura -->
                                        <?php if (isset($_SESSION['user_id']) && (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'])): ?>
                                            <?php if (in_array($job['id'], $applied_job_ids)): ?>
                                                <button type="button" class="btn btn-success" disabled>Registrado</button>
                                            <?php else: ?>
                                                <form method="POST" class="apply-form">
                                                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                                    <button type="submit" name="apply" class="btn btn-primary">Candidatar-se</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Mensagem para usuários não logados -->
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="login-prompt">
                        <a href="login.php" class="btn btn-primary">Inicia sesión para postularte</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>
</body>
</html>
