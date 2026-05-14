<?php

namespace app\controller;

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use app\database\DB;
use DateTime;

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
            var_dump($e->getMessage());
        }
    }
    public function authenticate($request, $response)
    {
        $form = $request->getParsedBody();
        $login = $form['login'] ?? null;
        $senha = $form['senha'] ?? null;

        if (empty($login) || empty($senha)) {
            return $this->json($response, ['status' => false, 'msg' => 'Por favor informe seu usuário e senha!', 'id' => 0], 400);
        }

        // Rate Limiting via Session (Considere usar Redis em produção para maior escala)
        if (isset($_SESSION['login_locked_until']) && $_SESSION['login_locked_until'] > time()) {
            return $this->json($response, ['status' => false, 'msg' => 'Muitas tentativas. Tente novamente em breve.', 'id' => 0], 429);
        }

        try {
            $qb = DB::connection()->createQueryBuilder();
            $user = $qb->select('*')
                ->from('vw_user')
                ->where('cpf = :login OR email = :login OR whatsapp = :login')
                ->setParameter('login', $login)
                ->executeQuery()
                ->fetchAssociative();

            // Proteção contra timing attack
            $dummyHash = '$2y$10$CwTycUXWue0Thq9StjUM0uJ8.k3.kK1m3Sv7lJ1uG9N9Yvb.MqYsa';
            $senhaValida = password_verify($senha, $user['senha'] ?? $dummyHash);

            if (!$user || !$senhaValida) {
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['login_locked_until'] = time() + 900;
                    $_SESSION['login_attempts'] = 0;
                }
                return $this->json($response, ['status' => false, 'msg' => 'Credenciais inválidas.', 'id' => 0], 403);
            }

            // Limpeza após sucesso
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
            session_regenerate_id(true);

            // Rehash se necessário
            if (password_needs_rehash($user['senha'], PASSWORD_DEFAULT)) {
                DB::connection()->update(
                    'users',
                    ['senha' => password_hash($senha, PASSWORD_DEFAULT), 'atualizado_em' => date('Y-m-d H:i:s')],
                    ['id' => $user['id']]
                );
            }

            $userId = (string)$user['id'];
            unset($user['senha']); // Segurança: nunca manter hash na memória de sessão

            // Configurações do JWT e Cookie
            $lifetime = (int)(ini_get('session.gc_maxlifetime') ?: 3600);
            $payload = [
                'iat' => time(),
                'exp' => time() + $lifetime,
                'sub' => $userId,
            ];

            $jwt = JWT::encode($payload, SECRET_KEY, 'HS256');

            // Use uma constante definida no seu config para o domínio
            $cookieDomain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : 'HS256';
            $isSecure = $request->getUri()->getScheme() === 'https';

            setcookie('auth_token', $jwt, [
                'expires'  => time() + $lifetime,
                'path'     => '/',
                'domain'   => $cookieDomain,
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            // Dados para retorno e sessão
            $expiraEm = (new DateTime())->modify("+{$lifetime} seconds")->format('Y-m-d H:i:s');
            $_SESSION['user'] = array_merge($user, [
                'logado' => true,
                'sessao_criada_em' => (new DateTime())->format('Y-m-d H:i:s'),
                'sessao_expira_em' => $expiraEm
            ]);

            return $this->json($response, [
                'status' => true,
                'msg'    => 'Bem-vindo!',
                'id'     => $userId,
                'sessao_expira_em' => $expiraEm
            ]);
        } catch (\Throwable $e) {
            error_log("[Auth Error] " . $e->getMessage());
            return $this->json($response, ['status' => false, 'msg' => 'Erro interno no servidor.'], 500);
        }
    }
}
