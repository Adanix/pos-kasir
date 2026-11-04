<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRoleController extends Controller
{
    public function index(): View
    {
        $permissions = RolePermission::orderBy('role')->orderBy('module')->get()->groupBy('role');
        $modules = ['pos', 'inventory', 'cashbook', 'reports', 'attendance', 'users'];
        $roles = ['admin', 'cashier', 'warehouse'];

        return view('admin.roles.index', compact('permissions', 'modules', 'roles'));
    }

    public function update(Request $request): RedirectResponse
    {
        $matrix = $request->input('matrix', []);

        foreach ($matrix as $id => $actions) {
            $perm = RolePermission::find($id);
            if ($perm) {
                // Admin role should always retain core view permissions
                $perm->update([
                    'can_view' => isset($actions['can_view']),
                    'can_create' => isset($actions['can_create']),
                    'can_edit' => isset($actions['can_edit']),
                    'can_delete' => isset($actions['can_delete']),
                ]);
            }
        }

        return redirect()->route('admin.roles.index')->with('success', 'Matriks hak akses (Role Permissions) berhasil diperbarui.');
    }
}
