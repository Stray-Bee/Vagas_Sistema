<?php
// Inicia a sessão para poder acessá-la
session_start();

// Remove todas as variáveis da sessão
$_SESSION = [];

// Destrói a sessão completamente
session_destroy();

// Redireciona o usuário de volta para a página inicial
header("Location: index.php");
exit;
?>