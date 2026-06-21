<?php

declare(strict_types=1);

namespace app\database;

use Doctrine\Migrations\DependencyFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MakeMigrationCommand extends Command
{
    private DependencyFactory $dependencyFactory;

    // Adicionamos o construtor para receber a factory que seu script envia
    public function __construct(DependencyFactory $dependencyFactory)
    {
        $this->dependencyFactory = $dependencyFactory;

        // Passamos o nome do comando para o construtor pai do Symfony
        parent::__construct('make:migration');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Cria uma migration compatível com o Doctrine')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Descrição curta para o nome da classe (ex: add_users_table)',
                ''
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $name = (string) $input->getArgument('name');

        $timestamp = date('YmdHis');
        $suffix = $name !== '' ? $this->sanitizeName($name) : '';
        $className = "Version{$timestamp}{$suffix}";
        $fileName = "{$className}.php";

        // BÔNUS: Pegando o diretório configurado direto do Doctrine em vez de deixar hardcoded!
        $configuration = $this->dependencyFactory->getConfiguration();
        $directories = $configuration->getMigrationDirectories();

        // Pega o primeiro diretório configurado ou cai no fallback
        $dir = !empty($directories) ? current($directories) : dirname(__DIR__) . '/database/migration';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = "{$dir}/{$fileName}";

        if (file_exists($path)) {
            $output->writeln("<error>Migration já existe.</error>");
            return Command::FAILURE;
        }

        file_put_contents(
            $path,
            $this->buildTemplate($className)
        );

        $output->writeln("<info>Migration criada com sucesso:</info> {$fileName}");

        return Command::SUCCESS;
    }

    private function buildTemplate(string $className): string
    {
        // Descobre o namespace dinamicamente a partir das configurações do Doctrine
        $configuration = $this->dependencyFactory->getConfiguration();
        $directories = $configuration->getMigrationDirectories();
        $namespace = !empty($directories) ? key($directories) : 'DoctrineMigrations';

        return <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration.
 */
final class {$className} extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(): void
    {
        // Exemplo de uso correto no Doctrine 3+:
        // \$this->addSql('CREATE TABLE ...');
    }

    public function down(): void
    {
        // \$this->addSql('DROP TABLE ...');
    }
}
PHP;
    }

    private function sanitizeName(string $name): string
    {
        return str_replace(
            ' ',
            '',
            ucwords(str_replace(['_', '-'], ' ', $name))
        );
    }
}
