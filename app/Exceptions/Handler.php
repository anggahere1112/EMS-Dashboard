<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle 419 CSRF token mismatch errors
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            // If it's an AJAX request, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session expired. Please refresh the page and try again.',
                    'redirect' => route('login')
                ], 419);
            }
            
            // For regular requests, redirect to login with message
            return redirect()->route('login')
                ->with('message', 'Your session has expired. Please login again.')
                ->with('alert-type', 'warning');
        }

        return parent::render($request, $exception);
    }
}
