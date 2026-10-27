<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Impersonate a user (Authority Login As).
     */
    public function impersonate(Request $request, User $user)
    {
        $currentUser = Auth::user();

        // Only admins/super-admins can impersonate
        $isAdmin = \Illuminate\Support\Facades\Gate::allows('*admin')
            || \Illuminate\Support\Facades\Gate::allows('admin')
            || (method_exists($currentUser, 'hasRole') && ($currentUser->hasRole('admin') || $currentUser->hasRole('super-admin') || $currentUser->hasRole('superadmin')))
            || (method_exists($currentUser, 'hasAnyRole') && $currentUser->hasAnyRole(['admin', 'super-admin', 'superadmin']));

        if (!$isAdmin) {
            abort(403, 'Unauthorized. Only administrators can impersonate users.');
        }

        // Prevent impersonating yourself
        if ($currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        // Save original admin ID in session if not already impersonating
        if (!session()->has('impersonator_id')) {
            session(['impersonator_id' => $currentUser->id]);
            session(['impersonator_name' => $currentUser->name]);
        }

        Auth::loginUsingId($user->id);

        return redirect()->route('tyro-dashboard.index')
            ->with('success', "Logged in as {$user->name}. You are now viewing the system as this user.");
    }

    /**
     * Leave impersonation and return to the original admin account.
     */
    public function leave(Request $request)
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('tyro-dashboard.index');
        }

        $originalAdminId = session()->pull('impersonator_id');
        session()->forget('impersonator_name');

        if ($originalAdminId) {
            Auth::loginUsingId($originalAdminId);
        }

        return redirect()->route('tyro-dashboard.users.index')
            ->with('success', 'Returned to your administrator account.');
    }
}
