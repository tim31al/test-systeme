<?php

declare(strict_types=1);

namespace App\Exception;

use App\Interface\LoggedExceptionInterface;
use Exception;

/**
 * Обработанная ошибка (залогирована).
 */
class LoggedException extends Exception implements LoggedExceptionInterface {}