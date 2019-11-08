<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Command;

use Exception;
use Ouzo\Config;
use Ouzo\Db;
use Ouzo\Db\ModelDefinition;
use Ouzo\Db\TransactionalProxy;
use Ouzo\Migration;
use Ouzo\MigrationFailedException;
use Ouzo\MigrationProgressBar;
use Ouzo\SchemaMigration;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class MigrationRunnerCommand extends Command
{
    /* @var InputInterface */
    private $input;
    /* @var OutputInterface */
    private $output;
    /* @var bool */
    private $commitEarly;
    /* @var bool */
    private $force;
    /* @var bool */
    private $init;
    /* @var string[] */
    private $dirs;
    /* @var bool */
    private $reset;
    /* @var array */
    private $dbConfig = [];
    /* @var bool */
    private $noAnimations;

    public function configure()
    {
        $this->setName('migration:run')
            ->addOption('commit_early', 'c', InputOption::VALUE_NONE, 'Commit after each migration')
            ->addOption('reset', 'r', InputOption::VALUE_NONE, 'Remove all previous migrations')
            ->addOption('init', 'i', InputOption::VALUE_NONE, 'Add schema_migrations table')
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories with migrations (separated by comma)')
            ->addOption('db_name', 'N', InputOption::VALUE_REQUIRED, 'Database name')
            ->addOption('db_user', 'U', InputOption::VALUE_REQUIRED, 'Database user')
            ->addOption('db_pass', 'S', InputOption::VALUE_REQUIRED, 'Database password')
            ->addOption('db_host', 'H', InputOption::VALUE_REQUIRED, 'Database host')
            ->addOption('db_port', 'P', InputOption::VALUE_REQUIRED, 'Database port')
            ->addOption('db_driver', 'D', InputOption::VALUE_REQUIRED, 'Database driver')
            ->addOption('no_animations', 'a', InputOption::VALUE_NONE, 'Disable animations (e.g. progress bar)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force confirmation');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->commitEarly = $this->input->getOption('commit_early');
        $this->force = $this->input->getOption('force');
        $this->init = $this->input->getOption('init');
        $this->dirs = $this->input->getOption('dir') ?: ['.'];
        $this->reset = $this->input->getOption('reset');
        $this->noAnimations = $this->input->getOption('no_animations');
        $this->dbConfig['dbname'] = $this->input->getOption('db_name') ?: Config::getValue('db', 'dbname');
        $this->dbConfig['user'] = $this->input->getOption('db_user') ?: Config::getValue('db', 'user');
        $this->dbConfig['pass'] = $this->input->getOption('db_pass') ?: Config::getValue('db', 'pass');
        $this->dbConfig['host'] = $this->input->getOption('db_host') ?: Config::getValue('db', 'host');
        $this->dbConfig['port'] = $this->input->getOption('db_port') ?: Config::getValue('db', 'port');
        $this->dbConfig['driver'] = $this->input->getOption('db_driver') ?: Config::getValue('db', 'driver');

        $this->migrate();
    }

    private function migrate()
    {
        Config::overrideProperty('db')->with($this->dbConfig);

        $this->output->writeln('=======================================================');
        $this->output->writeln("  Database = " . Objects::toString($this->dbConfig));
        $this->output->writeln("  Commit early = " . Objects::toString($this->commitEarly));
        $this->output->writeln("  Directory = " . Objects::toString($this->dirs));
        $this->output->writeln("  Initialize = " . Objects::toString($this->commitEarly));
        $this->output->writeln("  Force = " . Objects::toString($this->init));
        $this->output->writeln("  Reset = " . Objects::toString($this->reset));
        $this->output->writeln("  No animations = " . Objects::toString($this->noAnimations));
        $this->output->writeln('=======================================================');
        $this->output->writeln('');

        $db = $this->connectToDatabase();
        $this->initMigrations($db);
        $this->resetMigrations();

        $this->output->writeln("\nMigrations to apply:");
        $migrations = $this->loadMigrations();
        $this->output->writeln('');

        if (empty($migrations)) {
            $this->output->writeln('None. Bye!');
            return 0;
        }

        if (!$this->force) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Do you want to continue? [y/n] ', false);
            $question->setMaxAttempts(1);
            if (!$helper->ask($this->input, $this->output, $question)) {
                $this->output->writeln('What a bummer. Bye!');
                return 0;
            }
        }

        try {
            $self = $this->commitEarly ? $this : TransactionalProxy::newInstance($this);
            $self->runAll($db, $migrations);
            $this->output->writeln("\n\n<info>That's all. Bye!</info>");
            return 0;
        } catch (MigrationFailedException $ex) {
            $this->output->writeln("\n<error>Error</error>");
            $this->output->writeln("Could not apply migration {$ex->getClassName()} version {$ex->getVersion()}: {$ex->getMessage()}");
            $this->output->writeln($ex->getPrevious()->getTraceAsString());
            return 1;
        }
    }

    public function runAll(Db $db, array $migrations): void
    {
        $progressBar = $this->createProgressBar(count($migrations));

        foreach ($migrations as $version => $className) {
            $progressBar->displayMessage("[$version] $className");
            try {
                $db->runInTransaction(function () use ($className, $version, $db) {
                    $this->runSingleMigration($db, $className, $version);
                });
            } catch (Exception $ex) {
                throw new MigrationFailedException($ex, $className, $version);
            }
            $progressBar->advance();
        }

        $progressBar->finish();
    }

    private function runSingleMigration(Db $db, $className, $version): void
    {
        /** @var Migration $migration */
        $migration = new $className;
        $migration->run($db);
        SchemaMigration::create(['version' => $version, 'applied_at' => Clock::nowAsString()]);
    }

    private function loadMigrations(): array
    {
        $versions = Arrays::map(SchemaMigration::all(), Functions::extract()->version);

        $migrations = [];
        foreach ($this->dirs as $dir) {
            $migrations = array_replace($migrations, $this->loadMigrationsFromDir($dir, $versions));
        }
        ksort($migrations, SORT_STRING | SORT_ASC);
        foreach ($migrations as $version => $className) {
            $this->output->writeln(" [$version] $className");
        }
        return $migrations;
    }

    private function createProgressBar(int $max): MigrationProgressBar
    {
        return $this->noAnimations ? MigrationProgressBar::empty() : MigrationProgressBar::create($this->output, $max);
    }

    private function initMigrations(Db $db): void
    {
        if ($this->init) {
            $this->output->write("<info>Initializing migrations... </info>");
            if ($this->reset) {
                $db->execute("DROP TABLE IF EXISTS schema_migrations CASCADE");
            }
            $db->execute("CREATE TABLE schema_migrations(
                id SERIAL PRIMARY KEY,
                version TEXT,
                applied_at TIMESTAMP
            )");
            $this->output->writeln('<comment>DONE</comment>');
        }
    }

    private function connectToDatabase(): Db
    {
        $dbConfig = Objects::toString($this->dbConfig);
        $db = new Db(false);
        $this->output->write("<info>Connecting to db {$dbConfig}... </info>");
        $db->connectDb($this->dbConfig);
        SchemaMigration::$db = $db;
        ModelDefinition::resetCache();
        $this->output->writeln('<comment>DONE</comment>');
        return $db;
    }

    private function loadMigrationsFromDir(string $dir, array $versions): array
    {
        if (empty($dir)) {
            return [];
        }
        if (!file_exists($dir)) {
            throw new Exception("Migration directory `{$dir}` does not exist.");
        }
        $migrations = [];
        $files = scandir($dir, 0);
        for ($i = 2; $i < count($files); $i++) {
            $file = $files[$i];
            if (preg_match('/[0-9]{9,}_.+\.php/', $file)) {
                $path = $dir . '/' . $file;
                $version = substr($file, 0, strpos($file, '_'));
                if (is_file($path) && !in_array($version, $versions)) {
                    include_once($path);
                    $className = Strings::removeSuffix(substr($file, strpos($file, '_') + 1), '.php');
                    $migrations[$version] = $className;
                }
            }
        }
        return $migrations;
    }

    private function resetMigrations(): void
    {
        if ($this->reset) {
            $this->output->write("<info>Removing all migrations... </info>");
            SchemaMigration::where()->deleteAll();
            $this->output->writeln('<comment>DONE</comment>');
        }
    }
}