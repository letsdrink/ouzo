<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationProgressBar
{
    /* @var ProgressBar */
    private $progressBar;

    public function __construct(?ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
    }

    public static function create(OutputInterface $output, int $max)
    {
        ProgressBar::setFormatDefinition(
            'normal',
            "<info>Applying migration</info> <fg=cyan>%message%</>\n%current%/%max% [%bar%] %percent:3s%%"
        );
        $progressBar = new ProgressBar($output, $max);
        $progressBar->setMessage('');
        $progressBar->start();
        return new MigrationProgressBar($progressBar);
    }

    public static function empty()
    {
        return new MigrationProgressBar(null);
    }

    public function displayMessage(string $message)
    {
        if ($this->progressBar) {
            $this->progressBar->setMessage($message);
            $this->progressBar->display();
        }
    }

    public function advance()
    {
        if ($this->progressBar) {
            $this->progressBar->advance();
        }
    }

    public function finish()
    {
        if ($this->progressBar) {
            $this->progressBar->finish();
        }
    }
}