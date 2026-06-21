<?php

declare(strict_types=1);

namespace app\database\migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration para a tabela de contatos.
 */
final class Version20260621115252Contacts extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Criação da tabela de contatos relacionando com usuários';
    }

    public function up(Schema $schema): void
    {
        // Se você prefere usar SQL direto (addSql), pode descomentar as linhas abaixo e apagar o bloco do $schema:
        /*
        $this->addSql('
            CREATE TABLE contacts (
                id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                tipo VARCHAR(50) DEFAULT "" NOT NULL,
                valor VARCHAR(255) DEFAULT "" NOT NULL,
                criado_em DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                INDEX IDX_CONTACTS_USER (user_id),
                PRIMARY KEY(id),
                CONSTRAINT FK_CONTACTS_USERS FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        */

        // --- Versão usando a API do Schema do Doctrine ---
        $table = $schema->createTable('contacts');

        // Chave primária
        $table->addColumn('id', 'bigint', [
            'autoincrement' => true,
            'unsigned'      => true,
        ]);
        $table->setPrimaryKey(['id']);

        // Chave estrangeira para ligar com a tabela 'users'
        $table->addColumn('user_id', 'bigint', [
            'unsigned' => true,
            'notnull'  => true
        ]);

        // Campos do contato (Ex: Tipo = 'Telefone', 'LinkedIn' | Valor = '(11) 99999-0000', 'link...')
        $table->addColumn('tipo', 'string', [
            'length'  => 50,
            'default' => ''
        ]);

        $table->addColumn('valor', 'string', [
            'length'  => 255,
            'default' => ''
        ]);

        // Timestamps
        $table->addColumn('criado_em', 'datetime', [
            'notnull' => true,
            'default' => 'CURRENT_TIMESTAMP'
        ]);

        $table->addColumn('atualizado_em', 'datetime', [
            'notnull' => true,
            'default' => 'CURRENT_TIMESTAMP'
        ]);

        // Criando o relacionamento de chave estrangeira (Foreign Key)
        // Se o usuário for deletado, os contatos dele somem juntos (ON DELETE CASCADE)
        $table->addForeignKeyConstraint(
            'users',
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );

        // Index para otimizar buscas por usuário
        $table->addIndex(['user_id']);
    }

    public function down(Schema $schema): void
    {
        // Se usou sql direto no UP, use aqui também:
        // $this->addSql('DROP TABLE contacts');

        $schema->dropTable('contacts');
    }
}
