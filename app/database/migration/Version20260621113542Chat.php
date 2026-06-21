<?php

declare(strict_types=1);

namespace app\database\migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration para a tabela de mensagens do Chat.
 */
final class Version20260621113542Chat extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Criação da tabela de mensagens do chat (remetente, destinatário e conteúdo)';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('chats');

        // Chave primária da mensagem
        $table->addColumn('id', 'bigint', [
            'autoincrement' => true,
            'unsigned'      => true,
        ]);
        $table->setPrimaryKey(['id']);

        // Usuário que enviou a mensagem (Remetente)
        $table->addColumn('sender_id', 'bigint', [
            'unsigned' => true,
            'notnull'  => true
        ]);

        // Usuário que recebeu a mensagem (Destinatário)
        $table->addColumn('receiver_id', 'bigint', [
            'unsigned' => true,
            'notnull'  => true
        ]);

        // Conteúdo da mensagem (usando 'text' para mensagens longas)
        $table->addColumn('mensagem', 'text', [
            'notnull' => true
        ]);

        // Status de leitura (útil para mostrar o "visto" ou notificações de não lidas)
        $table->addColumn('lido', 'boolean', [
            'default' => false
        ]);

        // Timestamp de envio
        $table->addColumn('criado_em', 'datetime', [
            'notnull' => true,
            'default' => 'CURRENT_TIMESTAMP'
        ]);

        // --- Chaves Estrangeiras (Foreign Keys) ---
        // Se o remetente for deletado, apaga o histórico dele
        $table->addForeignKeyConstraint(
            'users',
            ['sender_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );

        // Se o destinatário for deletado, apaga o histórico dele
        $table->addForeignKeyConstraint(
            'users',
            ['receiver_id'],
            ['id'],
            ['onDelete' => 'CASCADE']
        );

        // --- Índices para Performance ---
        // Essencial para carregar a conversa entre dois usuários rapidamente
        $table->addIndex(['sender_id']);
        $table->addIndex(['receiver_id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('chats');
    }
}
