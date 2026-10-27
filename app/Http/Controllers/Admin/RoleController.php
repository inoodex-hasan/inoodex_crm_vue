<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
use HasinHayder\TyroDashboard\Http\Controllers\RoleController as BaseRoleController;

class RoleController extends BaseRoleController
{
    public function __construct()
    {
        $this->middleware('can:manage-roles');
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('manage-roles');

        $perPage = config('tyro-dashboard.pagination.roles', 15);

        $query = Role::withCount(['users', 'privileges']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $roles = $query->latest()->paginate($perPage)->withQueryString();
        $protectedRoles = config('tyro-dashboard.protected.roles', []);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'protectedRoles' => $protectedRoles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): Response
    {
        Gate::authorize('manage-roles');

        $privileges = Privilege::all();

        return Inertia::render('Roles/Create', [
            'privileges' => $privileges,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-roles');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug'],
            'privileges' => ['array'],
            'privileges.*' => ['exists:privileges,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role = new Role();
        $role->name = $validated['name'];
        $role->slug = $validated['slug'] ?: Str::slug($validated['name']);
        $role->is_active = $request->boolean('is_active', true);
        $role->save();

        if (!empty($validated['privileges'])) {
            $role->privileges()->sync($validated['privileges']);
        }

        return redirect()
            ->route('tyro-dashboard.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show($id): Response
    {
        Gate::authorize('manage-roles');

        $role = Role::with(['privileges', 'users'])->findOrFail($id);

        return Inertia::render('Roles/Show', [
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id): Response
    {
        Gate::authorize('manage-roles');

        $role = Role::with('privileges')->findOrFail($id);
        $privileges = Privilege::all();
        $protectedRoles = config('tyro-dashboard.protected.roles', []);

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'privileges' => $privileges,
            'protectedRoles' => $protectedRoles,
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('manage-roles');

        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug,' . $role->id],
            'privileges' => ['array'],
            'privileges.*' => ['exists:privileges,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role->name = $validated['name'];
        $role->slug = $validated['slug'] ?: Str::slug($validated['name']);
        if ($request->has('is_active')) {
            $role->is_active = $request->boolean('is_active');
        }
        $role->save();

        if (isset($validated['privileges'])) {
            $role->privileges()->sync($validated['privileges']);
        }

        return redirect()
            ->route('tyro-dashboard.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Toggle role status (Active/Inactive)
     */
    public function toggleStatus($id)
    {
        Gate::authorize('manage-roles');

        $role = Role::findOrFail($id);
        $role->is_active = !$role->is_active;
        $role->save();

        return redirect()
            ->back()
            ->with('success', 'Role status updated successfully.');
    }

    /**
     * Delete role.
     */
    public function destroy($id)
    {
        Gate::authorize('manage-roles');

        $role = Role::findOrFail($id);
        $protectedRoles = config('tyro-dashboard.protected.roles', []);

        if (in_array($role->slug, $protectedRoles) || in_array($role->id, $protectedRoles)) {
            return redirect()
                ->route('tyro-dashboard.roles.index')
                ->with('error', 'This role is protected and cannot be deleted.');
        }

        $role->users()->detach();
        $role->privileges()->detach();
        $role->delete();

        return redirect()
            ->route('tyro-dashboard.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Remove a user from the role.
     */
    public function removeUser($id, $userId)
    {
        Gate::authorize('manage-roles');

        $role = Role::findOrFail($id);
        $role->users()->detach($userId);

        return redirect()
            ->route('tyro-dashboard.roles.show', $id)
            ->with('success', 'User removed from role successfully.');
    }
}
