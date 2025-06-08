<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $query = User::query();

            // Filter by role if provided
            if ($request->has('role') && in_array($request->role, ['admin', 'employee'])) {
                $query->where('role', $request->role);
            }

            // Search by name or email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('employee_id', 'like', "%{$search}%");
                });
            }

            $users = $query->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $users->items(),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user with their leave statistics
     */
    public function show(User $user): JsonResponse
    {
        try {
            $authUser = Auth::user();

            // Employees can only view their own profile
            if ($authUser->isEmployee() && $authUser->id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get user's leave statistics
            $leaveStats = [
                'total_leaves' => $user->leaves()->count(),
                'pending_leaves' => $user->leaves()->pending()->count(),
                'approved_leaves' => $user->leaves()->approved()->count(),
                'rejected_leaves' => $user->leaves()->rejected()->count(),
            ];

            // Get recent leaves
            $recentLeaves = $user->leaves()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'department' => $user->department,
                        'employee_id' => $user->employee_id,
                        'joining_date' => $user->joining_date,
                        'created_at' => $user->created_at,
                    ],
                    'leave_statistics' => $leaveStats,
                    'recent_leaves' => $recentLeaves,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}