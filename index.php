<?php

$hotFile = __DIR__ . '/public/hot';

$isDev = file_exists($hotFile);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UC Final</title>

    <?php if ($isDev): ?>

        <?php $viteServer = trim(file_get_contents($hotFile)); ?>

        <!-- Vite HMR -->
        <script type="module" src="<?= $viteServer ?>/@vite/client"></script>
        <script type="module" src="<?= $viteServer ?>/resources/js/app.js"></script>

    <?php else: ?>

        <?php
        $manifestPath = __DIR__ . '/public/assets/manifest.json';

        $manifest = json_decode(
            file_get_contents($manifestPath),
            true
        );

        $app = $manifest['resources/js/app.js'];
        ?>

        <!-- CSS -->
        <?php if (!empty($app['css'])): ?>
            <?php foreach ($app['css'] as $css): ?>
                <link rel="stylesheet" href="/assets/<?= $css ?>">
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- JS -->
        <script type="module" src="/assets/<?= $app['file'] ?>"></script>

    <?php endif; ?>

</head>

<body>

    <div id="app"></div>

</body>

</html>