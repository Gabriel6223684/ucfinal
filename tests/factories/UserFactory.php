<?php

declare(strict_types=1);

// Dentro de um teste em tests/Integration ou tests/Feature
test('insere user com dados da factory', function () {
    $faker = Faker\Factory::create('pt_BR');

    $dados = [
        'nome_fantasia'       => $faker->company(),
        'sobrenome_razao'     => $faker->name(),
        'emal'                => $faker->email(),
        'senha'               => $faker->password_hash(),
        'ativo'               => true,
    ];

    $inserido = App\Database\Builder\InsertQuery::insert('user')
        ->save($dados);

    expect($inserido)->toBeTrue();
});