<?php

declare(strict_types=1);

namespace Shart\Middlewares;

use Closure;
use Shart\Services\LogService;

class LogExecutor
{
    /**
     * @var mixed
     */
    protected $log;

    public function __construct(LogService $log)
    {
        $this->log = $log;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);
        $this->log->execute();

        return $response;
    }
}
