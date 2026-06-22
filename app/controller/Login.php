<?php

declare(strict_types=1);

namespace app\controller;

use Firebase\JWT\JWT;

final class Login extends Base
{
    public function login($request, $response)
    {
        try {
            return $this->getTwig()
                ->render($response, $this->setView('login'), [
                    'titulo' => 'Início',
                ])
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
            return $this->json($response, [
                'status' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function authenticate($request, $response)
    {
        # Recupera as credenciais enviadas no corpo da requisição
        $formlogin = $request->getParsedBody();
        $login = $formlogin['login'] ?? null;
        $login_pass = $formlogin['senha'] ?? null;

        # Bloqueia se algum campo veio vazio
        if (is_null($login) || is_null($login_pass)) {
            return $this->json($response, ['status' => false, 'msg' => 'Por favor, informe seu email e senha!', 'id' => 0]);
        }

        # Verifica se a sessão está em "lockout" por excesso de tentativas falhas
        if (isset($_SESSION['login_locked_until']) && $_SESSION['login_locked_until'] > time()) {
            return $this->json($response, ['status' => false, 'msg' => 'Muitas tentativas. Tente novamente mais tarde.', 'id' => 0], 429);
        }

        try {
            # Começa a montar a query: SELECT * FROM vw_user
            $qb = \app\database\DB::select('*')
                ->from('vw_user');

            # Define o valor que será procurado nos três campos
            $placeholder = $qb->createNamedParameter($login);

            # Monta a cláusula WHERE com condições ligadas por OR:
            $qb->where('email = ' . $placeholder)
                ->orWhere('tel = ' . $placeholder);

            # Executa a query e busca um único registro
            $user = $qb->fetchAssociative();

            # Hash bcrypt pré-computado e inválido, usado quando o usuário não existe
            $dummyHash = '$2y$10$CwTycUXWue0Thq9StjUM0uJ8.k3.kK1m3Sv7lJ1uG9N9Yvb.MqYsa';

            # CORRIGIDO: Alterado de 'logn_pass' para 'senha' conforme banco de dados
            $login_passValida = password_verify($login_pass, $user['senha'] ?? $dummyHash);

            # Falha de autenticação: mensagem genérica + contador de tentativas
            if (!$user || !$login_passValida) {
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['login_locked_until'] = time() + 900;
                    $_SESSION['login_attempts'] = 0;
                }
                return $this->json($response, ['status' => false, 'msg' => 'Verifique seu e-mail e senha e tente novamente!', 'id' => 0], 403);
            }

            # Login válido: zera contadores de tentativa e lockout
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);

            # Regenera o ID da sessão para mitigar session fixation
            session_regenerate_id(true);

            # Renova o hash da senha se o algoritmo/custo padrão tiver mudado (Corrigido para 'senha')
            if (password_needs_rehash($user['senha'], PASSWORD_DEFAULT)) {
                \app\database\DB::connection()->update(
                    'users',
                    [
                        'senha'         => password_hash($login_pass, PASSWORD_DEFAULT),
                        'atualizado_em' => date('Y-m-d H:i:s'),
                    ],
                    ['id' => $user['id']],
                );
            }

            # Remove o hash da senha antes de gravar o usuário na sessão
            unset($user['senha']);

            # Persiste o usuário autenticado na sessão
            $_SESSION['user'] = $user;
            $_SESSION['user']['logado'] = true;

            $lifetime = (int) (ini_get('session.gc_maxlifetime') ?: 3600);
            $now = time();
            $jti = bin2hex(random_bytes(16));

            # Monta o payload do JWT
            $payload = [
                'iat' => $now,
                'nbf' => $now,
                'exp' => $now + $lifetime,
                'sub' => (string) $user['id'],
                'jti' => $jti,
            ];

            define('SECRET_KEY', '5a724404-69be-4adf-b6f3-ff45ab39afa1');

            $jwt = JWT::encode($payload, SECRET_KEY, 'HS256');

            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

            setcookie('auth_token', $jwt, [
                'expires'  => time() + $lifetime,
                'path'     => '/',
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            $agora = (new \DateTimeImmutable())->setTimestamp($now);
            $_SESSION['user']['sessao_criada_em'] = $agora->format('Y-m-d H:i:s');
            $_SESSION['user']['sessao_expira_em'] = $agora->modify("+{$lifetime} seconds")->format('Y-m-d H:i:s');

            return $this->json($response, [
                'status'           => true,
                'msg'              => 'Seja bem vindo de volta!',
                'id'               => $user['id'],
                'sessao_expira_em' => $_SESSION['user']['sessao_expira_em']
            ], 200);
        } catch (\PDOException $e) {
            error_log('[auth][DB] ' . $e->getMessage());
            return $this->json($response, ['status' => false, 'msg' => 'Não foi possível concluir o login. Tente novamente.', 'id' => 0], 500);
        } catch (\UnexpectedValueException | \DomainException $e) {
            error_log('[auth][JWT] ' . $e->getMessage());
            return $this->json($response, ['status' => false, 'msg' => 'Não foi possível concluir o login. Tente novamente.', 'id' => 0], 500);
        } catch (\Throwable $e) {
            error_log('[auth][GERAL] ' . $e->getMessage());
            return $this->json($response, ['status' => false, 'msg' => 'Erro inesperado. Tente novamente.', 'id' => 0], 500);
        }
    }

    public function Register($request, $response)
    {
        try {
            $form = $request->getParsedBody();
            $name = $form['nome'] ?? null;
            $reg_email = $form['email'] ?? null;
            $reg_pass     = $form['reg_pass'] ?? null;
            $tel  = $form['tel'] ?? null;

            if (!$name || !$reg_pass || !$reg_email) {
                return $this->json($response, ['status' => false, 'msg' => 'Preencha todos os campos obrigatórios!'], 400);
            }

            // CORRIGIDO: Nomes das chaves alterados para bater com a migration ('nome', 'senha', 'email', 'tel')
            $DataUser = [
                'nome'          => $name,
                'email'         => $reg_email,
                'tel'           => $tel ?? '',
                'senha'         => password_hash($reg_pass, PASSWORD_DEFAULT),
                'ativo'         => true,
                'administrador' => false,
                'criado_em'     => date('Y-m-d H:i:s'),
                'atualizado_em' => date('Y-m-d H:i:s')
            ];

            # Insere os dados no database com o Doctrine e recupera o ID corretamente via conexão
            \app\database\DB::connection()->insert('users', $DataUser);
            $id_usuario = \app\database\DB::connection()->lastInsertId();

            # CORRIGIDO: Inserindo na tabela 'contacts' com as colunas corretas ('user_id', 'tipo', 'valor')
            if ($reg_email) {
                $DataEmail = [
                    'user_id' => $id_usuario,
                    'tipo'    => 'EMAIL',
                    'valor'   => $reg_email
                ];
                \app\database\DB::connection()->insert('contacts', $DataEmail);
            }

            if ($tel) {
                $DataTel = [
                    'user_id' => $id_usuario,
                    'tipo'    => 'TELEFONE',
                    'valor'   => $tel
                ];
                \app\database\DB::connection()->insert('contacts', $DataTel);
            }

            return $this->json($response, [
                'status' => true,
                'msg'    => 'Cadastro realizado com sucesso!'
            ], 200);
        } catch (\Throwable $e) {
            error_log('[Register][ERRO] ' . $e->getMessage());
            return $this->json($response, [
                'status' => false,
                'msg'    => 'Erro interno ao realizar cadastro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout($request, $response)
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        setcookie('auth_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
