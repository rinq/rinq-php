<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Internal\Service;

/**
 * Service is an interface for background tasks that can finish with an error.
 */
interface Service
{
    /**
     * Run the service until {@see Service::stop()} is called.
     */
    public function wait();

    /**
     * Stop halts the service immediately.
     */
    public function stop();
}
