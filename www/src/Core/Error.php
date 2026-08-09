<?php
/**
 * Error and exception handling for the whole application.
 *
 * Both handlers are registered in index.php before anything else runs. PHP
 * errors are converted into ErrorException and thrown, so a notice becomes an
 * exception.
 *
 * An exception carrying code 404 produces a 404 response, everything else 500.
 * CONFIG['show_errors'] then decides what the visitor sees: the exception with
 * its stack trace, or the matching 404.php / 500.php view. If that view cannot
 * be rendered, a hardcoded HTML page is echoed, so an error page always exists
 * even when the framework is too broken to render one.
 *
 * Copyright (c) 2021 Jaanus Saarnak
 * SPDX-License-Identifier: MIT
 */

declare(strict_types=1);

namespace Core;

use ErrorException;
use RuntimeException;
use Throwable;

/**
 * Centralized error and exception handler.
 */
class Error
{
    /**
     * Flag to prevent infinite error handling loops.
     * @var bool
     */
    private static bool $isHandlingError = false;

    /**
     * Convert PHP errors into ErrorExceptions.
     *
     * @param int $level The error level
     * @param string $message The error message
     * @param string $file The file where the error occurred
     * @param int $line The line number
     * @throws ErrorException For any error, including ones written with @
     */
    public static function errorHandler(int $level, string $message, string $file, int $line): void
    {
        if (error_reporting() !== 0) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle uncaught exceptions.
     *
     * @param Throwable $exception The uncaught exception
     * @return void
     */
    public static function exceptionHandler(Throwable $exception): void
    {
        if (self::$isHandlingError) {
            return;
        }
        self::$isHandlingError = true;

        try {
            self::logError($exception);
        } catch (Throwable $logException) {
            @error_log("MVCLM Error (logging failed): " . $exception->getMessage());
        }

        $code = ($exception->getCode() === 404) ? 404 : 500;

        if (!headers_sent()) {
            http_response_code($code);
        }

        try {
            if (CONFIG['show_errors'] ?? false) {
                self::showDevelopmentError($exception);
            } else {
                self::showProductionError($code);
            }
        } catch (Throwable $displayException) {
            echo "<h1>Error</h1><p>An error occurred. Please try again later.</p>";
        }

        self::$isHandlingError = false;
    }

    /**
     * Display detailed error information in development mode.
     *
     * @param Throwable $exception The exception to display
     * @return void
     */
    private static function showDevelopmentError(Throwable $exception): void
    {
        echo "<h1>Fatal error</h1>";
        echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
        echo "<p>Message: '" . htmlspecialchars($exception->getMessage(), ENT_QUOTES) . "'</p>";
        echo "<p>Stack trace:<pre>" . htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES) . "</pre></p>";
        echo "<p>Thrown in '" . htmlspecialchars($exception->getFile(), ENT_QUOTES) . "' on line " . $exception->getLine() . "</p>";
        
        $logDir = self::getLogDirectory();
        if ($logDir !== null) {
            $logFile = $logDir . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
            echo "<hr><p><small>Error logged to: " . htmlspecialchars($logFile) . "</small></p>";
        }
    }

    /**
     * Display user-friendly error page in production mode.
     *
     * @param int $code The HTTP status code
     * @return void
     */
    private static function showProductionError(int $code): void
    {
        ob_start();
        $viewRendered = false;

        try {
            if ($code === 500) {
                // A complete HTML document. Do not route it through View, which
                // would wrap it in a layout and emit two documents.
                $errorView = CONFIG['path'] . '/App/Views/500.php';

                if (is_readable($errorView)) {
                    require $errorView;
                    $viewRendered = true;
                }
            } elseif (class_exists('Core\View')) {
                View::renderTemplate('/App/Views/' . $code . '.php', []);
                $viewRendered = true;
            }
        } catch (Throwable $viewException) {
            $viewRendered = false;
        }

        if ($viewRendered) {
            ob_end_flush();
        } else {
            ob_end_clean();
            self::showGenericErrorPage($code);
        }
    }

    /**
     * Show generic error page when View is unavailable.
     *
     * @param int $code The HTTP status code
     * @return void
     */
    private static function showGenericErrorPage(int $code): void
    {
        if ($code === 404) {
            echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head>";
            echo "<body><h1>404 - Page Not Found</h1>";
            echo "<p>The page you requested could not be found.</p>";
            echo "</body></html>";
        } else {
            echo "<!DOCTYPE html><html><head><title>Internal Server Error</title></head>";
            echo "<body><h1>Internal Server Error</h1>";
            echo "<p>We're sorry, but something went wrong. Please try again later.</p>";
            echo "</body></html>";
        }
    }

    /**
     * Log error details to file.
     *
     * Public so that a failure a controller has already caught and recovered
     * from still reaches the application log. Without it the only route into
     * that log is an uncaught exception, and error_log() writes somewhere else
     * entirely.
     *
     * @param Throwable $exception The exception to log
     * @return void
     */
    public static function logError(Throwable $exception): void
    {
        try {
            $logDirectory = self::getLogDirectory();
            
            if ($logDirectory === null) {
                @error_log(self::formatSimpleLogMessage($exception));
                return;
            }

            $logFile = $logDirectory . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
            $logMessage = self::formatLogMessage($exception, date('Y-m-d H:i:s'));
            
            if (@file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX) === false) {
                @error_log(self::formatSimpleLogMessage($exception));
            }
            
        } catch (Throwable $logException) {
            @error_log("MVCLM Error: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
        }
    }

    /**
     * Get or create a writable log directory.
     *
     * @return string|null The log directory path or null
     */
    private static function getLogDirectory(): ?string
    {
        static $cachedDirectory = null;
        
        if ($cachedDirectory !== null) {
            return $cachedDirectory;
        }

        $configPath = CONFIG['path'];
        
        if (!self::isAbsolutePath($configPath)) {
            $possibleBasePaths = [
                getcwd(),
                dirname($_SERVER['SCRIPT_FILENAME']),
                $_SERVER['DOCUMENT_ROOT']
            ];
            
            foreach ($possibleBasePaths as $basePath) {
                $testPath = $basePath . DIRECTORY_SEPARATOR . $configPath;
                if (is_dir($testPath)) {
                    $configPath = realpath($testPath);
                    break;
                }
            }
        }

        $potentialDirectories = [
            $configPath . DIRECTORY_SEPARATOR . 'logs',
            dirname($configPath) . DIRECTORY_SEPARATOR . 'logs',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mvclm_logs',
        ];

        foreach ($potentialDirectories as $dir) {
            if (self::ensureDirectoryExists($dir)) {
                $cachedDirectory = $dir;
                return $dir;
            }
        }

        return null;
    }

    /**
     * Check if a path is absolute.
     *
     * @param string $path The path to check
     * @return bool True if absolute
     */
    private static function isAbsolutePath(string $path): bool
    {
        return preg_match('/^[A-Z]:\\\\/', $path) || strpos($path, '/') === 0;
    }

    /**
     * Ensure a directory exists and is writable.
     *
     * @param string $directory The directory path
     * @return bool True if writable
     */
    private static function ensureDirectoryExists(string $directory): bool
    {
        if (is_dir($directory)) {
            return is_writable($directory);
        }

        $oldUmask = null;
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $oldUmask = umask(0);
        }

        $result = @mkdir($directory, 0777, true);
        
        if ($oldUmask !== null) {
            umask($oldUmask);
        }

        return $result && is_writable($directory);
    }

    /**
     * Format detailed log message.
     *
     * @param Throwable $exception The exception
     * @param string $timestamp The timestamp
     * @return string Formatted message
     */
    private static function formatLogMessage(Throwable $exception, string $timestamp): string
    {
        $message = "[{$timestamp}] ";
        $message .= "Uncaught " . get_class($exception) . ": ";
        $message .= $exception->getMessage() . "\n";
        $message .= "File: " . $exception->getFile() . "\n";
        $message .= "Line: " . $exception->getLine() . "\n";
        $message .= "Stack trace:\n" . $exception->getTraceAsString() . "\n";
        
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $message .= "Request: " . $_SERVER['REQUEST_METHOD'] . " " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";
        }
        
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $message .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
        }
        
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $message .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
        }
        
        $message .= "PHP Version: " . PHP_VERSION . "\n";
        $message .= "OS: " . PHP_OS . "\n";
        $message .= str_repeat('-', 80) . "\n\n";
        
        return $message;
    }

    /**
     * Format simple log message for fallback.
     *
     * @param Throwable $exception The exception
     * @return string Formatted message
     */
    private static function formatSimpleLogMessage(Throwable $exception): string
    {
        return sprintf(
            "MVCLM Error [%s]: %s: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
    }
}

