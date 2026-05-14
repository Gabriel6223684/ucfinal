<?php

namespace app\database;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;

final class Connection
{
    private static ?DBALConnection $instance = null;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public static function get(): DBALConnection
    {
        if (self::$instance === null) {
            // Configurações de performance e segurança
            $connectionParams = [
                'driver'   => 'pdo_pgsql',
                'host'     => $_ENV['DB_HOST'],
                'port'     => (int) ($_ENV['DB_PORT'] ?? 5432),
                'dbname'   => $_ENV['DB_NAME'],
                'user'     => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASSWORD'],
                'charset'  => 'UTF8',
                // Performance: Reutiliza a conexão PDO subjacente de forma eficiente
                'driverOptions' => [
                    \PDO::ATTR_PERSISTENT => false, // Geralmente false é melhor para ambientes conteinerizados
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ],
            ];

            // Validação de segurança: Não permitir conexão sem variáveis de ambiente essenciais
            if (!$_ENV['DB_HOST'] || !$_ENV['DB_PASSWORD']) {
                throw new Exception("Configurações de banco de dados ausentes no ambiente.");
            }

            self::$instance = DriverManager::getConnection($connectionParams);
        }

        return self::$instance;
    }

    // Impede clonagem e desserialização (Segurança do Singleton)
    private function __construct() {}
    private function __clone() {}
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Classe Proxy para facilitar o uso do Query Builder e Conexão
 */
class Database
{
    private static ?DBALConnection $db = null;

    public static function connection(): DBALConnection
    {
        if (self::$db === null) {
            self::$db = Connection::get();
        }
        return self::$db;
    }

    public static function qb(): QueryBuilder
    {
        return self::connection()->createQueryBuilder();
    }
}
