<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use HasinHayder\Tyro\Models\Role;
use HasinHayder\Tyro\Support\PasswordRules;
use HasinHayder\TyroDashboard\Http\Controllers\UserController as BaseUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends BaseUserController
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $perPage = config('tyro-dashboard.pagination.users', 15);

        $query = User::with('roles');

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->get('role')) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('slug', $role);
            });
        }

        // Filter by status
        if ($request->get('status') === 'suspended') {
            $query->whereNotNull('suspended_at');
        } elseif ($request->get('status') === 'active') {
            $query->whereNull('suspended_at');
        }

        $users = $query->latest()->paginate($perPage)->withQueryString();
        $roles = Role::all();
        $protectedUsers = config('tyro-dashboard.protected.users', []);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'protectedUsers' => $protectedUsers,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        $roles = Role::all();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => array_merge(['required', 'confirmed'], PasswordRules::get(['name' => $request->input('name'), 'email' => $request->input('email')])),
            'designation' => ['nullable', 'string', 'in:senior,junior'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'plain_password' => $validated['password'],
            'designation' => $validated['designation'] ?? null,
        ]);

        if (!empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()
            ->route('tyro-dashboard.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id): Response
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        $protectedUsers = config('tyro-dashboard.protected.users', []);

        return Inertia::render('Users/Edit', [
            'editUser' => $user,
            'roles' => $roles,
            'protectedUsers' => $protectedUsers,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'password' => array_merge(['nullable', 'confirmed'], PasswordRules::get(['name' => $request->input('name'), 'email' => $request->input('email')])),
            'designation' => ['nullable', 'string', 'in:senior,junior'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->username = $validated['username'];
        $user->designation = $validated['designation'] ?? $user->designation;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->plain_password = $validated['password'];
        }

        $user->save();

        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()
            ->route('tyro-dashboard.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Suspend the specified user.
     */
    public function suspend(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('tyro-dashboard.users.index')
                ->with('error', 'You cannot suspend yourself.');
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->suspend($validated['reason'] ?? null);

        return redirect()
            ->route('tyro-dashboard.users.index')
            ->with('success', 'User suspended successfully.');
    }

    /**
     * Unsuspend the specified user.
     */
    public function unsuspend($id)
    {
        $user = User::findOrFail($id);
        $user->unsuspend();

        return redirect()
            ->route('tyro-dashboard.users.index')
            ->with('success', 'User unsuspended successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('tyro-dashboard.users.index')
                ->with('error', 'You cannot delete yourself.');
        }

        $protectedUsers = config('tyro-dashboard.protected.users', []);
        if (in_array($user->id, $protectedUsers)) {
            return redirect()
                ->route('tyro-dashboard.users.index')
                ->with('error', 'This user is protected and cannot be deleted.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()
            ->route('tyro-dashboard.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Reset 2FA for the specified user.
     */
    public function reset2FA($id)
    {
        $user = User::findOrFail($id);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()
            ->back()
            ->with('success', 'Two-factor authentication has been reset for this user.');
    }

    /**
     * Impersonate the specified user.
     */
    public function loginAs($id)
    {
        $user = auth()->user();
        if (!$user || !($user->hasRole('admin') || $user->hasRole('super-admin'))) {
            abort(403, 'Unauthorized action.');
        }

        $targetUser = User::findOrFail($id);

        if ($targetUser->id === auth()->id()) {
            return redirect()
                ->route('tyro-dashboard.users.index')
                ->with('error', 'You cannot impersonate yourself.');
        }

        session(['impersonator_id' => auth()->id()]);
        auth()->login($targetUser);

        return redirect()
            ->route('tyro-dashboard.index')
            ->with('success', "Now impersonating {$targetUser->name}.");
    }
}
