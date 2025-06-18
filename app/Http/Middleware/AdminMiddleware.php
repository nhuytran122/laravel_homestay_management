<?php

namespace App\Http\Middleware;

use App\Enums\RoleSystem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $allowedRoles = [
            RoleSystem::MANAGER->value,
            RoleSystem::STAFF->value,
            RoleSystem::HOUSEKEEPER->value,
        ];

        /** @var \App\Models\User|\Spatie\Permission\Traits\HasRoles $user */
        if ($user->hasAnyRole($allowedRoles)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}