<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use RuntimeException;

class StaleFetchException extends RuntimeException implements ShouldRetryException 
{
}
