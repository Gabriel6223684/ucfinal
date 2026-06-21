<?php

declare(strict_types=1);

namespace app\database\migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration para Doctrine.
 */
final class Version20260621113323Users extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Criação da tabela de utilizadores';
    }

    public function up(Schema $schema): void
    {
        // Criação da tabela usando o Schema do Doctrine
        $table = $schema->createTable('users');

        // Chave primária (id) com auto-incremento (BigInt)
        $table->addColumn('id', 'bigint', [
            'autoincrement' => true,
            'unsigned'      => true,
        ]);
        $table->setPrimaryKey(['id']);

        // Outros campos
        $table->addColumn('nome', 'string', [
            'length'  => 150,
            'default' => ''
        ]);

        $table->addColumn('email', 'string', [
            'length'  => 255,
            'default' => ''
        ]);

        $table->addColumn('tel', 'string', [
            'length'  => 14,
            'default' => ''
        ]);

        $table->addColumn('senha', 'string', [
            'length'  => 255,
            'default' => ''
        ]);

        $table->addColumn('ativo', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('administrador', 'boolean', [
            'default' => false
        ]);

        // Carimbo de data/hora para criação e atualização
        $table->addColumn('criado_em', 'datetime', [
            'notnull' => true,
            'default' => 'CURRENT_TIMESTAMP'
        ]);

        $table->addColumn('atualizado_em', 'datetime', [
            'notnull' => true,
            'default' => 'CURRENT_TIMESTAMP'
        ]);
    }

    public function down(Schema $schema): void
    {
        // Remove a tabela caso seja feito um rollback
        $schema->dropTable('users');
    }
}
