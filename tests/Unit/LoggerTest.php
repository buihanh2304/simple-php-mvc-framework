<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use RuntimeException;
use System\Classes\Logger;
use Tests\TestCase;

class LoggerTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir() . DS . 'k-mvc-logger-' . uniqid('', true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->directory);

        parent::tearDown();
    }

    public function testInfoWritesFormattedLineToDailyLogFile(): void
    {
        $logger = new Logger($this->directory);
        $logger->info('Application started');

        $contents = $this->readTodayLog();

        $this->assertMatchesRegularExpression(
            '/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] INFO: Application started\n$/',
            $contents
        );
    }

    public function testLogInterpolatesContextPlaceholders(): void
    {
        $logger = new Logger($this->directory);
        $logger->info('User {id} logged in from {ip}', [
            'id' => 42,
            'ip' => '127.0.0.1',
        ]);

        $this->assertStringContainsString('User 42 logged in from 127.0.0.1', $this->readTodayLog());
        $this->assertStringNotContainsString('{id}', $this->readTodayLog());
    }

    public function testLogAppendsRemainingContextAsJson(): void
    {
        $logger = new Logger($this->directory);
        $logger->warning('Slow query', [
            'sql' => 'SELECT 1',
            'ms' => 120,
        ]);

        $contents = $this->readTodayLog();

        $this->assertStringContainsString('WARNING: Slow query', $contents);
        $this->assertStringContainsString('"sql":"SELECT 1"', $contents);
        $this->assertStringContainsString('"ms":120', $contents);
    }

    public function testLogSkipsMessagesBelowConfiguredLevel(): void
    {
        $logger = new Logger($this->directory, 'error');
        $logger->debug('noise');
        $logger->info('still noise');
        $logger->warning('almost');
        $logger->error('boom');

        $contents = $this->readTodayLog();

        $this->assertStringNotContainsString('noise', $contents);
        $this->assertStringNotContainsString('almost', $contents);
        $this->assertStringContainsString('ERROR: boom', $contents);
    }

    public function testLogCreatesMissingDirectory(): void
    {
        $this->assertDirectoryDoesNotExist($this->directory);

        (new Logger($this->directory))->debug('created');

        $this->assertDirectoryExists($this->directory);
        $this->assertFileExists($this->todayLogPath());
    }

    public function testLogAppendsToExistingFile(): void
    {
        $logger = new Logger($this->directory);
        $logger->info('first');
        $logger->info('second');

        $contents = $this->readTodayLog();

        $this->assertStringContainsString('INFO: first', $contents);
        $this->assertStringContainsString('INFO: second', $contents);
        $this->assertSame(2, substr_count($contents, "\n"));
    }

    public function testLogIncludesExceptionDetails(): void
    {
        $logger = new Logger($this->directory);
        $exception = new RuntimeException('disk full');

        $logger->error('Backup failed', ['exception' => $exception]);

        $contents = $this->readTodayLog();

        $this->assertStringContainsString('ERROR: Backup failed', $contents);
        $this->assertStringContainsString('RuntimeException', $contents);
        $this->assertStringContainsString('disk full', $contents);
        $this->assertStringNotContainsString('"exception"', $contents);
    }

    public function testLogRejectsInvalidLevel(): void
    {
        $logger = new Logger($this->directory);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid log level [fatal].');

        $logger->log('fatal', 'nope');
    }

    public function testLevelHelpersWriteExpectedLabels(): void
    {
        $logger = new Logger($this->directory);
        $logger->debug('d');
        $logger->info('i');
        $logger->notice('n');
        $logger->warning('w');
        $logger->error('e');
        $logger->critical('c');
        $logger->alert('a');
        $logger->emergency('m');

        $contents = $this->readTodayLog();

        $this->assertStringContainsString('DEBUG: d', $contents);
        $this->assertStringContainsString('INFO: i', $contents);
        $this->assertStringContainsString('NOTICE: n', $contents);
        $this->assertStringContainsString('WARNING: w', $contents);
        $this->assertStringContainsString('ERROR: e', $contents);
        $this->assertStringContainsString('CRITICAL: c', $contents);
        $this->assertStringContainsString('ALERT: a', $contents);
        $this->assertStringContainsString('EMERGENCY: m', $contents);
    }

    private function todayLogPath(): string
    {
        return $this->directory . DS . date('Y-m-d') . '.log';
    }

    private function readTodayLog(): string
    {
        $path = $this->todayLogPath();

        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (glob($directory . DS . '*') ?: [] as $file) {
            @unlink($file);
        }

        @rmdir($directory);
    }
}
