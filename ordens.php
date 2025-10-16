<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oficina_db";

$conexao = new mysqli($servername, $username, $password, $dbname);
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// CADASTRAR ORDEM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_ordem'])) {
    $data_ordem = $_POST['data_ordem'];
    $descricao = $_POST['descricao'];
    $modelo_veiculo = $_POST['modelo_veiculo'];
    $placa_veiculo = $_POST['placa_veiculo'];
    $nome_cliente = $_POST['nome_cliente'];
    $servicos_selecionados = isset($_POST['servicos']) ? $_POST['servicos'] : [];

    // Inserir ordem
    $sql_ordem = "INSERT INTO ordemservico (data_ordem, descricao) VALUES ('$data_ordem', '$descricao')";
    if ($conexao->query($sql_ordem) === TRUE) {
        $id_ordem = $conexao->insert_id;
        
        // Inserir veículo
        $sql_veiculo = "INSERT INTO veiculo (modelo_veiculo, placa_veiculo, id_ordem) VALUES ('$modelo_veiculo', '$placa_veiculo', '$id_ordem')";
        if ($conexao->query($sql_veiculo) === TRUE) {
            $id_veiculo = $conexao->insert_id;
            
            // Inserir cliente
            $sql_cliente = "INSERT INTO cliente (nome_cliente, id_veiculo) VALUES ('$nome_cliente', '$id_veiculo')";
            if ($conexao->query($sql_cliente) === TRUE) {
                
                // Inserir serviços selecionados na tabela inclui
                foreach ($servicos_selecionados as $id_servico) {
                    $sql_inclui = "INSERT INTO inclui (id_ordem, id_servico) VALUES ('$id_ordem', '$id_servico')";
                    $conexao->query($sql_inclui);
                }
                
                $success = "Ordem cadastrada com sucesso!";
            }
        }
    } else {
        $error = "Erro ao cadastrar ordem: " . $conexao->error;
    }
}

// EXCLUIR ORDEM
if (isset($_GET['excluir_ordem'])) {
    $id = $_GET['excluir_ordem'];
    
    // Excluir da tabela inclui primeiro
    $conexao->query("DELETE FROM inclui WHERE id_ordem = '$id'");
    
    // Encontrar veículo relacionado
    $sql_veiculo = "SELECT id_veiculo FROM veiculo WHERE id_ordem = '$id'";
    $result = $conexao->query($sql_veiculo);
    if ($result->num_rows > 0) {
        $veiculo = $result->fetch_assoc();
        $id_veiculo = $veiculo['id_veiculo'];
        
        // Excluir cliente
        $conexao->query("DELETE FROM cliente WHERE id_veiculo = '$id_veiculo'");
        
        // Excluir veículo
        $conexao->query("DELETE FROM veiculo WHERE id_veiculo = '$id_veiculo'");
    }
    
    // Excluir ordem
    if ($conexao->query("DELETE FROM ordemservico WHERE id_ordem = '$id'") === TRUE) {
        $success = "Ordem excluída com sucesso!";
    } else {
        $error = "Erro ao excluir ordem: " . $conexao->error;
    }
}

// LISTAR SERVIÇOS DISPONÍVEIS
$sql_servicos = "SELECT * FROM servico ORDER BY tipo_servico";
$resultado_servicos = $conexao->query($sql_servicos);

// LISTAR ORDENS COM SERVIÇOS
$sql_ordens = "SELECT 
                o.id_ordem, 
                o.data_ordem, 
                o.descricao, 
                c.nome_cliente, 
                v.modelo_veiculo, 
                v.placa_veiculo,
                GROUP_CONCAT(s.tipo_servico SEPARATOR ', ') as servicos,
                SUM(s.preco_servico) as total
               FROM ordemservico o 
               LEFT JOIN veiculo v ON o.id_ordem = v.id_ordem 
               LEFT JOIN cliente c ON v.id_veiculo = c.id_veiculo 
               LEFT JOIN inclui i ON o.id_ordem = i.id_ordem
               LEFT JOIN servico s ON i.id_servico = s.id_servico
               GROUP BY o.id_ordem
               ORDER BY o.id_ordem DESC";
$resultado_ordens = $conexao->query($sql_ordens);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordens de Serviço</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Ordens de Serviço</h1>
        <nav>
            <a href="index.php" class="btn btn-secondary">Início</a>
            <a href="clientes.php" class="btn btn-secondary">Clientes</a>
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
        <!-- CARD CADASTRO ORDEM -->
        <div class="card">
            <h2>Nova Ordem de Serviço</h2>
            <form method="POST" class="user-form">
                <input type="hidden" name="cadastrar_ordem" value="1">
                
                <div class="form-group">
                    <label for="data_ordem">Data:</label>
                    <input type="date" name="data_ordem" required>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea name="descricao" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="modelo_veiculo">Modelo do Veículo:</label>
                    <input type="text" name="modelo_veiculo" required>
                </div>

                <div class="form-group">
                    <label for="placa_veiculo">Placa:</label>
                    <input type="text" name="placa_veiculo" required>
                </div>

                <div class="form-group">
                    <label for="nome_cliente">Nome do Cliente:</label>
                    <input type="text" name="nome_cliente" required>
                </div>

                <div class="form-group">
                    <label>Serviços:</label>
                    <div class="servicos-list">
                        <?php if ($resultado_servicos->num_rows > 0): ?>
                            <?php while ($servico = $resultado_servicos->fetch_assoc()): ?>
                                <div class="servico-item">
                                    <input type="checkbox" name="servicos[]" value="<?= $servico['id_servico'] ?>" id="servico_<?= $servico['id_servico'] ?>">
                                    <label for="servico_<?= $servico['id_servico'] ?>">
                                        <?= $servico['tipo_servico'] ?> - R$ <?= number_format($servico['preco_servico'], 2, ',', '.') ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="no-data">Nenhum serviço cadastrado. <a href="servicos.php">Cadastre serviços primeiro</a></p>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Cadastrar Ordem</button>
            </form>
        </div>

        <!-- CARD LISTAGEM ORDENS -->
        <div class="card">
            <h2>Ordens Cadastradas</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Veículo</th>
                            <th>Serviços</th>
                            <th>Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_ordens->num_rows > 0): ?>
                            <?php while ($ordem = $resultado_ordens->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $ordem['id_ordem'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($ordem['data_ordem'])) ?></td>
                                    <td><?= $ordem['nome_cliente'] ?? 'N/A' ?></td>
                                    <td><?= $ordem['modelo_veiculo'] ?? 'N/A' ?></td>
                                    <td><?= $ordem['servicos'] ?? 'Nenhum serviço' ?></td>
                                    <td>R$ <?= number_format($ordem['total'] ?? 0, 2, ',', '.') ?></td>
                                    <td class="actions">
                                        <a href="?excluir_ordem=<?= $ordem['id_ordem'] ?>" class="btn btn-delete" onclick="return confirm('Excluir esta ordem?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="no-data">Nenhuma ordem encontrada.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>