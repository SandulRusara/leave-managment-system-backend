<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest;
use App\Http\Requests\UpdateLeaveStatusRequest;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = Leave::with(['user', 'approvedBy']);


            if ($user->isEmployee()) {
                $query->where('user_id', $user->id);
            }


            if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $request->status);
            }


            if ($user->isAdmin() && $request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }


            $leaves = $query->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'leaves' => $leaves->items(),
                    'pagination' => [
                        'current_page' => $leaves->currentPage(),
                        'last_page' => $leaves->lastPage(),
                        'per_page' => $leaves->perPage(),
                        'total' => $leaves->total(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch leaves',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(LeaveRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();


            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            $leave = Leave::create([
                'user_id' => $user->id,
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            $leave->load(['user', 'approvedBy']);

            return response()->json([
                'success' => true,
                'message' => 'Leave request submitted successfully',
                'data' => ['leave' => $leave]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit leave request',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Leave $leave): JsonResponse
    {
        try {
            $user = Auth::user();


            if ($user->isEmployee() && $leave->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this leave request'
                ], 403);
            }

            $leave->load(['user', 'approvedBy']);

            return response()->json([
                'success' => true,
                'data' => ['leave' => $leave]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch leave details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateLeaveStatusRequest $request, Leave $leave): JsonResponse
    {
        try {
            $user = Auth::user();


            if (!$leave->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This leave request has already been processed'
                ], 400);
            }

            $leave->update([
                'status' => $request->status,
                'admin_comments' => $request->admin_comments,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            $leave->load(['user', 'approvedBy']);

            $action = $request->status === 'approved' ? 'approved' : 'rejected';

            return response()->json([
                'success' => true,
                'message' => "Leave request {$action} successfully",
                'data' => ['leave' => $leave]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update leave status',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Leave $leave): JsonResponse
    {
        try {
            $user = Auth::user();


            if ($user->isEmployee() && ($leave->user_id !== $user->id || !$leave->isPending())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own pending leave requests'
                ], 403);
            }

            $leave->delete();

            return response()->json([
                'success' => true,
                'message' => 'Leave request deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete leave request',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function statistics(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $totalLeaves = Leave::count();
            $pendingLeaves = Leave::pending()->count();
            $approvedLeaves = Leave::approved()->count();
            $rejectedLeaves = Leave::rejected()->count();
            $totalEmployees = User::where('role', 'employee')->count();


            $monthlyStats = Leave::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();


            $monthlyLeaves = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyLeaves[] = $monthlyStats[$i] ?? 0;
            }


            $leaveTypeStats = Leave::selectRaw('leave_type, COUNT(*) as count')
                ->groupBy('leave_type')
                ->pluck('count', 'leave_type')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_leaves' => $totalLeaves,
                        'pending_leaves' => $pendingLeaves,
                        'approved_leaves' => $approvedLeaves,
                        'rejected_leaves' => $rejectedLeaves,
                        'total_employees' => $totalEmployees,
                    ],
                    'monthly_leaves' => $monthlyLeaves,
                    'leave_type_stats' => $leaveTypeStats,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}