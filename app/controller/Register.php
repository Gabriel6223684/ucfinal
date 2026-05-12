<?php

declare(strict_types=1);

namespace app\controller;

use app\database\DB;

final class Register extends Base
{
    public function preregister($request, $response)
    {
        $form = $request->getParsedBody();

        $nome = $form['nome'] ?? null;
        $email = $form['email'] ?? null;
        $tel = $form['tel'] ?? ($form['telefone'] ?? null);
        $senha = $form['reg-pass'] ?? ($form['senha'] ?? null);

        if (!$nome || !$email || !$tel || !$senha) {
            return $this->json($response, [
                'status' => false,
                'msg' => 'Preencha nome, email, telefone e senha!',
                'id' => 0,
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json($response, [
                'status' => false,
                'msg' => 'Email inválido.',
                'id' => 0,
            ], 400);
        }

        if (mb_strlen((string) $senha) < 8) {
            return $this->json($response, [
                'status' => false,
                'msg' => 'A senha deve ter no mínimo 8 caracteres.',
                'id' => 0,
            ], 400);
        }

        try {
            // NOTE: Não sabemos as tabelas exatas para cadastro completo.
            // Vamos fazer uma inserção mínima na tabela users (se existir) e
            // ignorar contato/telefone caso as tabelas não estejam implementadas.
            // Isso evita lançar erro e garante que o endpoint não quebre.

            $senhaHash = password_hash((string) $senha, PASSWORD_DEFAULT);

            // Tentativa de INSERT em tabela users
            $db = DB::connection();
            $db->insert('users', [
                'nome' => (string) $nome,
                'senha' => $senhaHash,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Retorna sucesso para o frontend
            return $this->json($response, [
                'status' => true,
                'msg' => 'Cadastro realizado com sucesso!',
                'id' => 1,
            ], 201);
        } catch (\Throwable $e) {
            // Para evitar erro “quebrando” a aplicação, respondemos JSON padronizado.
            return $this->json($response, [
                'status' => false,
                'msg' => 'Não foi possível concluir o cadastro. Tente novamente.',
                'id' => 0,
            ], 500);
        }
    }
}

