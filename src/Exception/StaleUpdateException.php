<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use RuntimeException;

class StaleUpdateException extends RuntimeException implements ShouldRetryException 
{
}
