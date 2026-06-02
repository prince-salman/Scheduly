<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ── Auth pages ─────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:30',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
            'status'   => 'pending',
        ]);

        Auth::login($user);

        return redirect()->route('pending');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        return match ($user->status) {
            'pending'  => redirect()->route('pending'),
            'rejected' => redirect()->route('rejected'),
            default    => redirect()->intended(route('dashboard')),
        };
    }

    public function pending()
    {
        $user = auth()->user();

        return match ($user->status) {
            'approved' => redirect()->route('dashboard'),
            'rejected' => redirect()->route('rejected'),
            default    => view('auth.pending'),
        };
    }

    public function rejected()
    {
        $user = auth()->user();

        return match ($user->status) {
            'approved' => redirect()->route('dashboard'),
            'pending'  => redirect()->route('pending'),
            default    => view('auth.rejected', ['reason' => $user->reason]),
        };
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        // Clean up rejected accounts on logout
        if ($user && $user->isRejected()) {
            $user->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ── Admin: user list ───────────────────────────────────────────

    public function index(Request $request)
    {
        $query = User::query();

        // Filter by status tab
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Search by name or email
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all'      => User::count(),
            'approved' => User::where('status', 'approved')->count(),
            'pending'  => User::where('status', 'pending')->count(),
            'rejected' => User::where('status', 'rejected')->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    // ── Admin: user detail ─────────────────────────────────────────

    public function detail($id)
    {
        $user = User::findOrFail($id);

        $taskStats = [
            'total'   => Task::forUser($id)->count(),
            'done'    => Task::forUser($id)->done()->count(),
            'focus_h' => round(Task::forUser($id)->sum('focus_minutes') / 60, 1),
        ];

        $recentTasks = Task::forUser($id)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.users.detail', compact('user', 'taskStats', 'recentTasks'));
    }

    // ── Admin: create user manually ────────────────────────────────

    public function create()
    {
        return view('admin.users.create');
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:30',
            'role'     => 'required|in:admin,user',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
            'status'   => 'approved',
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    // ── Admin: approve ─────────────────────────────────────────────

    public function approve(Request $request, $id)
    {
        $request->validate([
            'role' => 'nullable|in:admin,user',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'status' => 'approved',
            'role'   => $request->input('role', $user->role),
            'reason' => null,
        ]);

        return back()->with('success', "Akun {$user->name} berhasil disetujui.");
    }

    // ── Admin: reject ──────────────────────────────────────────────

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'status' => 'rejected',
            'reason' => $request->reason,
        ]);

        return back()->with('success', "Akun {$user->name} berhasil ditolak.");
    }

    // ── Admin: update role ─────────────────────────────────────────

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,member',
        ]);

        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return back()->with('success', "Role {$user->name} diperbarui.");
    }

    // ── Admin: toggle active / deactivate ─────────────────────────

  public function toggleStatus(Request $request, $id)
{
    $user = User::findOrFail($id);

    if ($user->status === 'approved') {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $user->update([
            'status' => 'rejected',
            'reason' => $request->reason,
        ]);

        // Force logout user: hapus semua session milik user ini
        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        $msg = "Akun {$user->name} dinonaktifkan.";
    } else {
        $user->update([
            'status' => 'approved',
            'reason' => null,
        ]);
        $msg = "Akun {$user->name} diaktifkan kembali.";
    }

    return back()->with('success', $msg);
}
}