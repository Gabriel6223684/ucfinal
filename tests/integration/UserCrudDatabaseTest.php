<?php

declare(strict_types=1);

test('ciclo CRUD completo no banco PostgreSQL', function () {

    // 1. INSERT — cria um registro real no banco
    $inserido = \App\Database\Builder\InsertQuery::insert('user')
        ->save([
            'nome_fantasia'       => 'Teste Integração',
            'sobrenome_razao'     => 'Razão Teste',
            'ativo'               => true,
        ]);

    expect($inserido)->toBeTrue();

    // 2. SELECT — busca o registro recém-criado
    $user = \App\Database\Builder\SelectQuery::select()
        ->from('user')
        ->fetch();

    expect($user)->not->toBeEmpty();
    expect($user['nome_fantasia'])->toBe('Teste Integração');

    $id = $user['id'];

    // 3. UPDATE — altera o registro
    $atualizado = \App\Database\Builder\UpdateQuery::table('user')
        ->set(['nome_fantasia' => 'Teste Alterado'])
        ->where('id', '=', $id)
        ->update();

    expect($atualizado)->toBeTrue();

    // Confirma que a alteração persistiu
    $userAlterado = \App\Database\Builder\SelectQuery::select()
        ->from('user')
        ->where('id', '=', $id)
        ->fetch();

    expect($userAlterado['nome_fantasia'])->toBe('Teste Alterado');

    // 4. DELETE — remove o registro de teste
    $deletado = \App\Database\Builder\DeleteQuery::table('user')
        ->where('id', '=', $id)
        ->delete();

    expect($deletado)->toBeTrue();

    // Confirma que não existe mais
    $userRemovido = \App\Database\Builder\SelectQuery::select()
        ->from('user')
        ->where('id', '=', $id)
        ->fetch();

    expect($userRemovido)->toBeEmpty();
});
