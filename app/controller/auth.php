<?php
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Ação inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($action === 'login') {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['login-pass'] ?? '';

        if (!$email || empty($password)) {
            $response['message'] = 'Por favor, preencha todos os campos corretamente.';
        } else {
            // Aqui você faria a consulta no Banco de Dados
            // Exemplo fictício:
            $response = ['success' => true, 'message' => 'Login realizado com sucesso!'];
        }
    }

    if ($action === 'register') {
        $nome  = htmlspecialchars($_POST['nome'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $tel   = htmlspecialchars($_POST['tel'] ?? '');
        $pass  = $_POST['reg-pass'] ?? '';

        // Validações básicas
        if (empty($nome) || !$email || empty($pass)) {
            $response['message'] = 'Preencha todos os campos obrigatórios.';
        } elseif (strlen($pass) < 8) {
            $response['message'] = 'A senha deve ter no mínimo 8 caracteres.';
        } else {
            // Aqui você inseriria no Banco de Dados (usando password_hash para a senha!)
            $response = ['success' => true, 'message' => 'Conta criada com sucesso!'];
        }
    }
}

echo json_encode($response);
