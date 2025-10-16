<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oficina_db";

$conexao = new mysqli($servername, $username, $password, $dbname);
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// CADASTRAR SERVIÇO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_servico'])) {
    $tipo_servico = $_POST['tipo_servico'];
    $preco_servico = $_POST['preco_servico'];
    
    $sql = "INSERT INTO servico (tipo_servico, preco_servico) VALUES ('$tipo_servico', '$preco_servico')";
    if ($conexao->query($sql) === TRUE) {
        $success = "Serviço cadastrado com sucesso!";
    } else {
        $error = "Erro ao cadastrar serviço: " . $conexao->error;
    }
}

// EXCLUIR SERVIÇO
if (isset($_GET['excluir_servico'])) {
    $id = $_GET['excluir_servico'];

    if ($conexao->query("DELETE FROM servico WHERE id_servico = '$id'") === TRUE) {
        $success = "Serviço excluído com sucesso!";
    } else {
        $error = "Erro ao excluir serviço: " . $conexao->error;
    }
}

// LISTAR SERVIÇOS
$sql_servicos = "SELECT * FROM servico ORDER BY id_servico DESC";
$resultado_servicos = $conexao->query($sql_servicos);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Serviços</h1>
        <nav>
            <a href="index.php" class="btn btn-secondary">Início</a>
            <a href="ordens.php" class="btn btn-secondary">Ordens</a>
            <a href="clientes.php" class="btn btn-secondary">Clientes</a>
        </nav>
    </header>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <div class="cards-grid">
        <!-- CARD CADASTRO SERVIÇO -->
        <div class="card">
            <h2>Cadastrar Serviço</h2>
            <form method="POST" class="user-form">
                <input type="hidden" name="cadastrar_servico" value="1">
                
                <div class="form-group">
                    <label for="tipo_servico">Tipo de Serviço:</label>
                    <input type="text" name="tipo_servico" required>
                </div>

                <div class="form-group">
                    <label for="preco_servico">Preço:</label>
                    <input type="number" step="0.01" name="preco_servico" required>
                </div>

                <button type="submit" class="btn btn-primary">Cadastrar Serviço</button>
            </form>
        </div>

        <!-- CARD LISTAGEM SERVIÇOS -->
        <div class="card">
            <h2>Serviços Cadastrados</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo de Serviço</th>
                            <th>Preço</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_servicos->num_rows > 0): ?>
                            <?php while ($servico = $resultado_servicos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $servico['id_servico'] ?></td>
                                    <td><?= $servico['tipo_servico'] ?></td>
                                    <td>R$ <?= number_format($servico['preco_servico'], 2, ',', '.') ?></td>
                                    <td class="actions">
                                        <a href="?excluir_servico=<?= $servico['id_servico'] ?>" class="btn btn-delete" onclick="return confirm('Excluir este serviço?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="no-data">Nenhum serviço cadastrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>