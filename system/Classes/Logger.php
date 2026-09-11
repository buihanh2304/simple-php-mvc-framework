<?php

/*
// This file is a part of K-MVC
// version: 2.x
// author: MrKen
// website: https://vdevs.net
// github: https://github.com/buihanh2304/simple-php-mvc-framework
*/

namespace System\Classes;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class Logger
{
    public const DEBUG = 'debug';
    public const INFO = 'info';
    public const NOTICE = 'notice';
    public const WARNING = 'warning';
    public const ERROR = 'error';
    public const CRITICAL = 'critical';
    public const ALERT = 'alert';
    public const EMERGENCY = 'emergency';

    protected const LEVELS = [
        self::DEBUG => 100,
        self::INFO => 200,
        self::NOTICE => 250,
        self::WARNING => 300,
        self::ERROR => 400,
        self::CRITICAL => 500,
        self::ALERT => 550,
        self::EMERGENCY => 600,
    ];

    protected string $directory;
    protected string $minLevel;

    public function __construct(?string $directory = null, ?string $minLevel = null)
    {
        $this->directory = rtrim(
            $directory ?? (string) config('system.log.path', SYSTEM . 'files' . DS . 'logs'),
            '/\\'
        );
        $this->minLevel = strtolower(
            $minLevel ?? (string) config('system.log.level', self::DEBUG)
        );

        if (!isset(self::LEVELS[$this->minLevel])) {
            $this->minLevel = self::DEBUG;
        }
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log(self::DEBUG, $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log(self::NOTICE, $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log(self::ALERT, $message, $context);
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->log(self::EMERGENCY, $message, $context);
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $level = strtolower($level);

        if (!isset(self::LEVELS[$level])) {
            throw new InvalidArgumentException("Invalid log level [{$level}].");
        }

        if (self::LEVELS[$level] < self::LEVELS[$this->minLevel]) {
            return;
        }

        $exception = null;

        if (isset($context['exception']) && $context['exception'] instanceof Throwable) {
            $exception = $context['exception'];
            unset($context['exception']);
        }

        $message = $this->interpolate($message, $context);

        $line = sprintf('[%s] %s: %s', date('Y-m-d H:i:s'), strtoupper($level), $message);

        if ($context !== []) {
            $encoded = json_encode(
                $context,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            );

            if ($encoded !== false && $encoded !== '[]' && $encoded !== '{}') {
                $line .= ' ' . $encoded;
            }
        }

        if ($exception) {
            $line .= PHP_EOL . $exception;
        }

        $this->write($line . PHP_EOL);
    }

    protected function interpolate(string $message, array &$context): string
    {
        $replace = [];

        foreach ($context as $key => $value) {
            if (!is_scalar($value) && $value !== null) {
                continue;
            }

            $placeholder = '{' . $key . '}';

            if (!str_contains($message, $placeholder)) {
                continue;
            }

            $replace[$placeholder] = $this->stringify($value);
            unset($context[$key]);
        }

        return strtr($message, $replace);
    }

    protected function stringify(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        return (string) $value;
    }

    protected function write(string $line): void
    {
        if (!is_dir($this->directory) && !@mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new RuntimeException("Unable to create log directory [{$this->directory}].");
        }

        $path = $this->directory . DS . date('Y-m-d') . '.log';

        if (file_put_contents($path, $line, FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException("Unable to write log file [{$path}].");
        }
    }
}
