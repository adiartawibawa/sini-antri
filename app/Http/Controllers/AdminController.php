<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\QueueSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (! auth()->user() || auth()->user()->is_operator) {
                    abort(403, 'Akses khusus Administrator.');
                }

                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $stats = [
            'total_today' => Antrian::whereDate('created_at', today())->count(),
            'completed' => Antrian::where('status', 'completed')->whereDate('created_at', today())->count(),
            'skipped' => Antrian::where('status', 'skipped')->whereDate('created_at', today())->count(),
            'waiting' => Antrian::waiting()->count(),
        ];

        $recentQueues = Antrian::with('operator')
            ->latest()
            ->take(5)
            ->get();

        $operators = User::where('is_operator', true)->get();

        return view('admin.dashboard', compact('stats', 'recentQueues', 'operators'));
    }

    public function settings()
    {
        $setting = QueueSetting::firstOrCreate([], [
            'prefix' => 'A',
            'avg_service_minutes' => 5,
            'reset_daily' => true,
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        return view('admin.settings', compact('setting'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'required|string|max:5',
            'avg_service_minutes' => 'required|integer|min:1',
            'max_queue_limit' => 'required|integer|min:0',
            'reset_daily' => 'boolean',
            'is_system_open' => 'boolean',
            'youtube_url' => 'nullable|string|max:255',
        ]);

        $setting = QueueSetting::first();
        $setting->update($validated);

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function operators()
    {
        $operators = User::where('is_operator', true)->get();

        return view('admin.operators', compact('operators'));
    }

    public function storeOperator(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'loket_name' => 'required|string|max:255',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'loket_name' => $validated['loket_name'],
            'is_operator' => true,
            'is_active' => true,
        ]);

        return back()->with('success', 'Operator berhasil ditambahkan.');
    }

    public function updateOperator(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'loket_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return back()->with('success', 'Data operator diperbarui.');
    }

    public function deleteOperator(User $user)
    {
        if (! $user->is_operator) {
            return back()->with('error', 'Tidak dapat menghapus Admin.');
        }
        $user->delete();

        return back()->with('success', 'Operator dihapus.');
    }

    public function resetCounter()
    {
        $setting = QueueSetting::first();
        $setting->update(['current_counter' => 0]);

        return back()->with('success', 'Counter antrian telah direset ke 0.');
    }
}
