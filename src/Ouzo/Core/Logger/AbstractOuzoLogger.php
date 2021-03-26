<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Closure;
use Ouzo\Config;
use Ouzo\Utilities\Arrays;
use Psr\Log\AbstractLogger;

abstract class AbstractOuzoLogger extends AbstractLogger
{
    private MessageFormatter $messageFormatter;
    private ?array $minimalLevels = [];
    private ?array $loggerConfiguration;

    public function __construct(private string $name, string $configuration)
    {
        $messageFormatterClass = DefaultMessageFormatter::class;
        $loggerConfiguration = Config::getValue('logger', $configuration);
        if ($loggerConfiguration) {
            $messageFormatterClass = Arrays::getValue($loggerConfiguration, 'formatter', $messageFormatterClass);
            $this->minimalLevels = Arrays::getValue($loggerConfiguration, 'minimal_levels');
        }
        $this->messageFormatter = new $messageFormatterClass();
        $this->loggerConfiguration = $loggerConfiguration;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    protected function logWithFunction(Closure $writeToLogFunction, string $level, string $message, array $params): void
    {
        $ouzoLevel = LogLevelTranslator::toSyslogLevel($level);
        $minimalLevel = $this->minimalLevels ? Arrays::getValue($this->minimalLevels, $this->name, LOG_DEBUG) : LOG_DEBUG;
        if ($ouzoLevel <= $minimalLevel) {
            $message = $this->messageFormatter->format($this->name, $level, $message);
            if (!empty($params)) {
                $message = sprintf($message, ...$params);
            }
            $writeToLogFunction($message);
        }
    }

    public function debug($message, array $context = []): void
    {
        if ($this->isDebug()) {
            parent::debug($message, $context);
        }
    }

    protected function isDebug(): bool
    {
        return Config::getValue('debug') === true;
    }

    protected function getLoggerConfiguration(): ?array
    {
        return $this->loggerConfiguration;
    }
}
