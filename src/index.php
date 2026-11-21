<?php
$host = 'db';
$dbname = 'sistema_cadastro';
$user = 'gabriel';
$pass = '5238';

$mensagem = '';
$tipoMensagem = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Processa o envio do formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        if ($nome && $email && $telefone) {
            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone) VALUES (:nome, :email, :telefone)");
                $stmt->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':telefone' => $telefone,
                ]);
                $mensagem = "Usuário cadastrado com sucesso!";
                $tipoMensagem = "sucesso";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $mensagem = "Erro: Este e-mail já está cadastrado.";
                } else {
                    $mensagem = "Erro ao inserir: " . $e->getMessage();
                }
                $tipoMensagem = "erro";
            }
        } else {
            $mensagem = "Preencha todos os campos.";
            $tipoMensagem = "erro";
        }
    }

    // Lista os registros para validar a inserção na mesma tela
    $usuarios = $pdo->query("SELECT id, nome, email, telefone, criado_em FROM usuarios ORDER BY id DESC")->fetchAll();

} catch (PDOException $e) {
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; background: #f4f4f9; color: #333; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { margin-top: 15px; padding: 10px 16px; background: #0066cc; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0052a3; }
        .alerta { padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .sucesso { background: #d4edda; color: #155724; }
        .erro { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Novo Cadastro</h2>

        <?php if ($mensagem): ?>
            <div class="alerta <?= $tipoMensagem ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" placeholder="(48) 99999-9999" required>

            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <div class="card">
        <h2>Usuários Cadastrados</h2>
        <?php if (empty($usuarios)): ?>
            <p>Nenhum usuário cadastrado ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['id']) ?></td>
                            <td><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['telefone']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>
