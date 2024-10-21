<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerAdapter
{
    private MessageFormatter $messageFormatter;
    private ?ContextEnhancer $contextEnhancer;
    private array $minimalLevels;

    public function __construct(
        private string $name,
        string $configuration,
        private LoggerInterface $logger,
    )
    {
        $loggerConfiguration = Config::getValue('logger', $configuration) ?: [];
        $messageFormatter = Arrays::getValue($loggerConfiguration, 'formatter', DefaultMessageFormatter::class);
        $contextEnhancer = Arrays::getValue($loggerConfiguration, 'context_enhancer');

        $this->messageFormatter = new $messageFormatter();
        $this->contextEnhancer = is_null($contextEnhancer) ? null : new $contextEnhancer();
        $this->minimalLevels = Arrays::getValue($loggerConfiguration, 'minimal_levels', []);
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        if ($this->isDebug()) {
            $this->log(LogLevel::DEBUG, $message, $context);
        }
    }

    public function log(string $level, $message, array $context = []): void
    {
        $ouzoLevel = LogLevelTranslator::toSyslogLevel($level);
        $minimalLevel = $this->minimalLevels ? Arrays::getValue($this->minimalLevels, $this->name, LOG_DEBUG) : LOG_DEBUG;
        if ($ouzoLevel <= $minimalLevel) {
            $message = $this->messageFormatter->format($this->name, $level, $message);
            if (!is_null($this->contextEnhancer)) {
                $context = $this->contextEnhancer->enhanceContext($context);
            }
            if (!empty($context)) {
                $message = Strings::sprintAssoc($message, $context);
            }
            $this->logger->log($level, $message, $context);
        }
    }

    private function isDebug(): bool
    {
        return Config::getValue('debug') === true;
    }
}
