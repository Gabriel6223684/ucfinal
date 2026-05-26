<?php

namespace app\controller;
declare(strict_types=1);

final class Home extends Base
{
    public function home($request, $response)
    {
        return $this->getTwig()
            ->render($response, $this->setView('home'), [
                'titulo' => 'Início',
            ])
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
}