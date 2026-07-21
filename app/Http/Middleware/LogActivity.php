<?php

namespace App\Http\Middleware;

use App\Models\Log as LogModel;
use App\Services\LogService;
use App\Services\ProfileService;
use App\Services\GroupService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Auto-write a row in the `logs` table after the response is generated.
 *
 * Usage in routes:
 *   Route::middleware('log.activity:profiles')->prefix('profiles')->group(...);
 *   Route::post('/delete/{id}', ...)->middleware('log.activity:profiles');
 *
 * If no target_type argument is given, it is inferred from the route's first
 * meaningful URI segment (skipping api/v1/v2/admin prefixes).
 *
 * `target_id` is taken from the URL `{id}` parameter when present, falling
 * back to the request body's `id` field. Bulk endpoints have no single id —
 * those entries will have `target_id = null`.
 */
class LogActivity
{
    private const SKIP_SEGMENTS = ['api', 'v1', 'v2', 'admin'];

    protected LogService $logService;
    protected ProfileService $profileService;
    protected GroupService $groupService;

    public function __construct(LogService $logService,
                                ProfileService $profileService,
                                GroupService $groupService)
    {
        $this->logService = $logService;
        $this->profileService = $profileService;
        $this->groupService = $groupService;
    }

    public function handle(Request $request, Closure $next, ?string $targetType = null)
    {
        // Resolve the target's name BEFORE the request runs — a delete wipes the
        // row, so reading it afterwards always comes back empty.
        $nameDeleted = null;
        try {
            $nameDeleted = $this->resolveDeletedName($request);
        } catch (\Throwable $e) {
            Log::error('LogActivity name lookup failed: ' . $e->getMessage(), [
                'exception' => $e,
                'path' => $request->path(),
            ]);
        }

        $response = $next($request);

        try {
            if (!$this->logService->isEnabled()) {
                return $response;
            }

            $route = $request->route();

            if ($targetType === null && $route) {
                foreach (explode('/', trim($route->uri(), '/')) as $segment) {
                    if ($segment === '' || str_starts_with($segment, '{')) {
                        continue;
                    }
                    if (in_array($segment, self::SKIP_SEGMENTS, true)) {
                        continue;
                    }
                    $targetType = $segment;
                    break;
                }
            }

            $targetId = $route?->parameter('id');
            if ($targetId === null) {
                $candidate = $request->input('id');
                $targetId = is_string($candidate) ? $candidate : null;
            }

            $statusCode = method_exists($response, 'getStatusCode')
                ? $response->getStatusCode()
                : 200;

            // Most APIs in this project respond with `{success, message, data}`
            // and may return success=false even on HTTP 200. Inspect the body
            // when it's JSON so the log type/message reflect the real outcome.
            $bodySuccess = null;
            $bodyMessage = null;
            $payload = $this->decodeJsonBody($response);
            if (is_array($payload)) {
                if (array_key_exists('success', $payload)) {
                    $bodySuccess = (bool) $payload['success'];
                }
                if (isset($payload['message']) && is_string($payload['message'])) {
                    $bodyMessage = $payload['message'];
                }
            }

            if ($statusCode >= 500) {
                $type = LogModel::TYPE_ERROR;
            } elseif ($statusCode >= 400 || $bodySuccess === false) {
                // 4xx, or 2xx with business-level failure -> warn
                $type = LogModel::TYPE_WARN;
            } else {
                $type = LogModel::TYPE_INFO;
            }

            $outcome = $bodySuccess === null
                ? (string) $statusCode
                : sprintf('%d %s', $statusCode, $bodySuccess ? 'success' : 'fail');

            $requestPath = $request->path();
            $message = sprintf(
                '[%s] /%s -> %s',
                $request->method(),
                $requestPath,
                $outcome
            );
            if ($bodyMessage !== null && $bodyMessage !== '') {
                $message .= ' | ' . $bodyMessage;
            }

            // Name captured before the request ran (see top of handle()).
            if ($nameDeleted !== null) {
                $message .= ' (' . $nameDeleted . ')';
            }

            $this->logService->create($targetId, $targetType, $type, $message);
        } catch (\Throwable $e) {
            // Logging must never break the response — record to laravel.log
            // and continue.
            Log::error('LogActivity middleware failed: ' . $e->getMessage(), [
                'exception' => $e,
                'path' => $request->path(),
                'method' => $request->method(),
            ]);
        }

        return $response;
    }

    /**
     * Name of the entity a `/delete/{id}` request is about to remove, or null
     * when the route isn't a delete or the entity can't be resolved.
     */
    private function resolveDeletedName(Request $request): ?string
    {
        $requestPath = $request->path();
        if (!str_contains($requestPath, '/delete/')) {
            return null;
        }

        $targetId = $request->route()?->parameter('id');
        if (!is_string($targetId) || $targetId === '') {
            return null;
        }

        if (str_contains($requestPath, '/profiles/')) {
            return $this->profileService->getName($targetId);
        }
        if (str_contains($requestPath, '/groups/')) {
            return $this->groupService->getName($targetId);
        }

        return null;
    }

    /**
     * Best-effort JSON decode of the response body. Returns null when the
     * response is not JSON or cannot be parsed. Skips streaming/binary
     * responses so we don't accidentally consume them.
     */
    private function decodeJsonBody($response): ?array
    {
        if ($response instanceof \Symfony\Component\HttpFoundation\StreamedResponse
            || $response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            return null;
        }

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            return is_array($data) ? $data : null;
        }

        if (!method_exists($response, 'getContent')) {
            return null;
        }

        $content = $response->getContent();
        if (!is_string($content) || $content === '') {
            return null;
        }

        $contentType = method_exists($response, 'headers')
            ? (string) $response->headers->get('Content-Type', '')
            : '';
        if ($contentType !== '' && !str_contains(strtolower($contentType), 'json')) {
            return null;
        }

        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : null;
    }
}
