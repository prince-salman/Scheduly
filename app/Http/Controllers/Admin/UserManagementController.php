<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * List all users — filterable by status.
     */
    public function index(Request $request)
    {
        // TODO: query users with optional status filter ($request->status),
        //       paginate results (10 per page), pass to view
        return view('admin.users.index');
    }

    /**
     * Show detail for a single user.
     */
    public function show($user)
    {
        // TODO: find user by ID (or fail), load their recent tasks,
        //       pass to detail view
        return view('admin.users.detail');
    }

    /**
     * Approve a pending user account.
     */
    public function approve(Request $request, $user)
    {
        // TODO: find user, set status='active', assign role from $request->role,
        //       send approval email, redirect with success flash
        $request->validate([
            'role' => 'nullable|in:admin,member,viewer',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil disetujui.');
    }

    /**
     * Reject a pending user account with a reason.
     */
    public function reject(Request $request, $user)
    {
        // TODO: find user, set status='rejected', store rejection reason,
        //       send rejection email, redirect with flash
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna telah ditolak.');
    }
}
