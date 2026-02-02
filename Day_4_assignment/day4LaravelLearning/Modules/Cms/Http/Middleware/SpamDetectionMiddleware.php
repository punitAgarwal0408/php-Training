<?php
namespace Modules\Cms\Http\Middleware;

use Closure;

class SpamDetectionMiddleware
{
    public function handle($request, Closure $next)
    {
        $content = $request->input('content');
        // Simple spam detection logic (customize as needed)
        $spamWords = ['spam', 'viagra', 'casino', 'free money'];
        foreach ($spamWords as $word) {
            if (stripos($content, $word) !== false) {
                return response()->json(['error' => 'Spam detected'], 422);
            }
        }
        return $next($request);
    }
}
