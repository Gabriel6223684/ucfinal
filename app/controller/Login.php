<?php

namespace app\controller;

use Firebase\JWT\JWT;

require_once __DIR__ . '/../vendor/autoload.php';

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
            error_log('[login][VIEW] ' . $e->getMessage());
            return $this->json($response, ['status' => false, 'msg' => 'Erro ao carregar a página.'], 500);
        }
    }

    public function authenticate($request, $response)
    {
        # Recupera as credenciais enviadas no corpo da requisição
        $formlogin = $request->getParsedBody();
        $login = $formlogin['login_email'] ?? null;
        $login_pass = $formlogin['login_pass'] ?? null;

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

            # Monta a cláusula WHERE com três condições ligadas por OR:
            $qb->where('login_email = ' . $placeholder)
                ->orWhere('tel = ' . $placeholder);

            # Executa a query e busca um único registro
            $user = $qb->fetchAssociative();

            # Hash bcrypt pré-computado e inválido, usado quando o usuário não existe
            $dummyHash = '$2y$10$CwTycUXWue0Thq9StjUM0uJ8.k3.kK1m3Sv7lJ1uG9N9Yvb.MqYsa';

            # Sempre executa password_verify, mesmo sem usuário, para manter tempo de resposta constante
            $login_passValida = password_verify($login_pass, $user['logn_pass'] ?? $dummyHash);

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

            # Renova o hash da senha se o algoritmo/custo padrão tiver mudado
            if (password_needs_rehash($user['login_pass'], PASSWORD_DEFAULT)) {
                \app\database\DB::connection()->update(
                    'users',
                    [
                        'login_pass'         => password_hash($login_pass, PASSWORD_DEFAULT),
                        'atualizado_em' => date('Y-m-d H:i:s'),
                    ],
                    ['id' => $user['id']],
                );
            }

            # Remove o hash da senha antes de gravar o usuário na sessão
            unset($user['login_pass']);

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
                #'iss' => HOST,
                #'aud' => HOST,
                'jti' => $jti,
            ];

            define('SECRET_KEY', '5a724404-69be-4adf-b6f3-ff45ab39afa1');

            $jwt = JWT::encode($payload, SECRET_KEY, 'HS256');

            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

            setcookie('auth_token', $jwt, [
                'expires'  => time() + $lifetime,
                'path'     => '/',
                'secure'   => true,
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
            return $this->json($response, ['status' => false, 'msg' => 'Erro inesperado. Tente novamente: ' . $e->getMessage(), 'id' => 0], 500);
        }
    }

    public function Register($request, $response)
    {
        $form = $request->getParsedBody();
        $name      = $form['name'] ?? null;
        $reg_pass     = $form['reg_pass'] ?? null;
        $reg_email     = $form['reg_email'] ?? null;
        $tel  = $form['tel'] ?? null;

        $DataUser = [
            'name'      => $name,
            'reg_pass'     => password_hash($reg_pass, PASSWORD_DEFAULT)
        ];

        # Insere os dados no database com o Doctrine e recupera o ID corretamente via conexão
        \app\database\DB::connection()->insert('users', $DataUser);
        $id_usuario = \app\database\DB::connection()->lastInsertId();

        # Insere os dados do email do usuário na base.
        $DataEmail = [
            'id_usuario' => $id_usuario,
            'tipo' => 'EMAIL',
            'contato' => $reg_email
        ];
        \app\database\DB::connection()->insert('contact', $DataEmail);

        # Insere os dados do telefone do usuário na base.
        $DataTel = [
            'id_usuario' => $id_usuario,
            'tipo' => 'TELEFONE',
            'contato' => $tel
        ];
        \app\database\DB::connection()->insert('contact', $DataTel);

        return $this->json($response, [
            'status' => true,
            'msg' => 'Cadastrado realizado com sucesso!'
        ], 200);
    }

    # 2. Opção de sair do sistema (Logout)
    public function logout($request, $response)
    {
        # Limpa os dados do array da sessão global
        $_SESSION = [];

        # Se desejar destruir o cookie da sessão PHP completamente
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

        # Destrói a sessão no servidor
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        # Invalida/limpa o cookie JWT do lado do cliente
        setcookie('auth_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        # Redireciona o cliente de volta para a tela de login
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
