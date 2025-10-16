<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oficina_db";

$conexao = new mysqli($servername, $username, $password, $dbname);
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// CADASTRAR CLIENTE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_cliente'])) {
    $nome_cliente = $_POST['nome_cliente'];
    
    $sql = "INSERT INTO cliente (nome_cliente) VALUES ('$nome_cliente')";
    if ($conexao->query($sql) === TRUE) {
        $success = "Cliente cadastrado com sucesso!";
    } else {
        $error = "Erro ao cadastrar cliente: " . $conexao->error;
    }
}

// EXCLUIR CLIENTE
if (isset($_GET['excluir_cliente'])) {
    $id = $_GET['excluir_cliente'];

    if ($conexao->query("DELETE FROM cliente WHERE id_cliente = '$id'") === TRUE) {
        $success = "Cliente excluído com sucesso!";
    } else {
        $error = "Erro ao excluir cliente: " . $conexao->error;
    }
}

// LISTAR CLIENTES
$sql_clientes = "SELECT id_cliente, nome_cliente FROM cliente ORDER BY id_cliente DESC";
$resultado_clientes = $conexao->query($sql_clientes);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Clientes</h1>
        <nav>
            <a href="index.php" class="btn btn-secondary">Início</a>
            <a href="ordens.php" class="btn btn-secondary">Ordens</a>
            <a href="servicos.php" class="btn btn-secondary">Serviços</a>
        </nav>
    </header>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <div class="cards-grid">
        <!-- CARD CADASTRO CLIENTE -->
        <div class="card">
            <h2>Cadastrar Cliente</h2>
            <form method="POST" class="user-form">
                <input type="hidden" name="cadastrar_cliente" value="1">
                
                <div class="form-group">
                    <label for="nome_cliente">Nome do Cliente:</label>
                    <input type="text" name="nome_cliente" required>
                </div>

                <button type="submit" class="btn btn-primary">Cadastrar Cliente</button>
            </form>
        </div>

        <!-- CARD LISTAGEM CLIENTES -->
        <div class="card">
            <h2>Clientes Cadastrados</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_clientes->num_rows > 0): ?>
                            <?php while ($cliente = $resultado_clientes->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $cliente['id_cliente'] ?></td>
                                    <td><?= $cliente['nome_cliente'] ?></td>
                                    <td class="actions">
                                        <a href="?excluir_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-delete" onclick="return confirm('Excluir este cliente?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="no-data">Nenhum cliente encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>