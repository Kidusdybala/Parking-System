<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandlePostTooLargeException
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the content length exceeds the post_max_size
        if ($request->server('CONTENT_LENGTH') > $this->getPostMaxSize()) {
            return redirect()->back()->withErrors([
                'file_too_large' => 'The uploaded file is too large. Maximum allowed size is ' .
                                   $this->getPostMaxSizeFormatted() . '.'
            ]);
        }

        return $next($request);
    }

    /**
     * Get the post_max_size in bytes
     */
    protected function getPostMaxSize(): int
    {
        $postMaxSize = ini_get('post_max_size');

        if (!$postMaxSize) {
            return 8 * 1024 * 1024; // Default to 8MB if not set
        }

        $unit = strtolower(substr($postMaxSize, -1));
        $number = (int)substr($postMaxSize, 0, -1);

        switch ($unit) {
            case 'g':
                return $number * 1024 * 1024 * 1024;
            case 'm':
                return $number * 1024 * 1024;
            case 'k':
                return $number * 1024;
            default:
                return (int)$postMaxSize;
        }
    }

    /**
     * Get the post_max_size formatted for display
     */
    protected function getPostMaxSizeFormatted(): string
    {
        return ini_get('post_max_size');
    }
}
