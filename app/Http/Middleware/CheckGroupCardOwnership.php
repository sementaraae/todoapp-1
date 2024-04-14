<?php

namespace App\Http\Middleware;

use App\Models\GroupCard;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupCardOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    { 
        $groupCardId = (int) $request->route('id');
        $groupCard = GroupCard::where('id',$groupCardId)->firstOrFail();
        // Check if the logged-in user owns the group card
        if ($groupCard->user_id !== Auth::id()) {
            return redirect()->route('groupCards.index');
        }
        return $next($request);
    }
}
