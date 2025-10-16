<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oficina_db";

$conexao = new mysqli($servername, $username, $password, $dbname);
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Oficina</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Sistema da Oficina</h1>
        <nav class="main-nav">
            <a href="ordens.php" class="btn btn-primary">Ordens de Serviço</a>
            <a href="clientes.php" class="btn btn-secondary">Clientes</a>
            <a href="servicos.php" class="btn btn-secondary">Serviços</a>
        </nav>
    </header>

    <div class="dashboard">
        <h2>Bem-vindo ao Sistema</h2>
        <p>Selecione uma opção no menu acima para gerenciar:</p>
        <div class="cards-grid">
            <div class="card">
                <h3>Ordens de Serviço</h3>
                <p>Gerencie ordens de serviço da oficina</p>
                <a href="ordens.php" class="btn btn-primary">Acessar</a>
            </div>
            <div class="card">
                <h3>Clientes</h3>
                <p>Cadastre e visualize clientes</p>
                <a href="clientes.php" class="btn btn-secondary">Acessar</a>
            </div>
            <div class="card">
                <h3>Serviços</h3>
                <p>Gerencie tipos de serviços e preços</p>
                <a href="servicos.php" class="btn btn-secondary">Acessar</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>