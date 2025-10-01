<?php

namespace App\Http\Middleware;

use App\Models\ActionLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogUserActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $method = $request->method();

        // Capture logout BEFORE it happens so Auth::check() is true
        if ($routeName === 'logout' && $method === 'POST' && Auth::check()) {
            $this->writeLog(
                userId: Auth::id(),
                action: 'user_logout',
                description: 'User logged out',
                request: $request
            );
        }

        $response = $next($request);

        // Log other actions AFTER request when user is authenticated
        if (Auth::check()) {
            $this->logAction($request, $response);
        }

        return $response;
    }

    /**
     * Log the user action
     */
    private function logAction(Request $request, Response $response): void
    {
        $user = Auth::user();
        $action = $this->determineAction($request);

        if (!$action) {
            return;
        }

        try {
            $this->writeLog($user->id, $action['action'], $action['description'], $request, $action['model_type'] ?? null, $action['model_id'] ?? null, $action['metadata'] ?? null);
        } catch (\Exception $e) {
            // Log error but don't break the request
            Log::error('Failed to log user action: ' . $e->getMessage());
        }
    }

    private function writeLog($userId, string $action, string $description, Request $request, $modelType = null, $modelId = null, $metadata = null): void
    {
        ActionLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'metadata' => $metadata,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Determine the action based on the request
     */
    private function determineAction(Request $request): ?array
    {
        $method = $request->method();
        $path = $request->path();
        $routeName = $request->route()?->getName();

        // Skip logging for certain routes
        if (in_array($path, ['api/action-logs', 'admin/action-logs']) ||
            str_contains($path, 'assets') ||
            str_contains($path, 'css') ||
            str_contains($path, 'js')) {
            return null;
        }

        // Login actions
        if ($routeName === 'login' && $method === 'POST') {
            return [
                'action' => 'user_login',
                'description' => 'User logged in',
            ];
        }

        // Logout actions
        if ($routeName === 'logout' && $method === 'POST') {
            return [
                'action' => 'user_logout',
                'description' => 'User logged out',
            ];
        }

        // Profile actions
        if (str_contains($path, 'profile')) {
            if ($method === 'PUT' || $method === 'PATCH') {
                return [
                    'action' => 'profile_updated',
                    'description' => 'User updated their profile',
                ];
            }
        }

        // Proposal actions
        if (str_contains($path, 'proposals')) {
            if ($method === 'POST') {
                return [
                    'action' => 'proposal_created',
                    'description' => 'User created a new proposal',
                ];
            } elseif ($method === 'PUT' || $method === 'PATCH') {
                return [
                    'action' => 'proposal_updated',
                    'description' => 'User updated a proposal',
                ];
            } elseif ($method === 'DELETE') {
                return [
                    'action' => 'proposal_deleted',
                    'description' => 'User deleted a proposal',
                ];
            }
        }

        // User management actions (admin only)
        if (str_contains($path, 'admin/users')) {
            if ($method === 'POST') {
                return [
                    'action' => 'user_created',
                    'description' => 'Admin created a new user',
                ];
            } elseif ($method === 'PUT' || $method === 'PATCH') {
                return [
                    'action' => 'user_updated',
                    'description' => 'Admin updated a user',
                ];
            } elseif ($method === 'DELETE') {
                return [
                    'action' => 'user_deleted',
                    'description' => 'Admin deleted a user',
                ];
            }
        }

        // Goal actions
        if (str_contains($path, 'goals')) {
            if ($method === 'POST') {
                return [
                    'action' => 'goal_created',
                    'description' => 'User created a new goal',
                ];
            } elseif ($method === 'PUT' || $method === 'PATCH') {
                return [
                    'action' => 'goal_updated',
                    'description' => 'User updated a goal',
                ];
            }
        }

        // Do not log simple dashboard page loads to avoid noise

        return null;
    }
}
