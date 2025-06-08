<?php

namespace Database\Seeders;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LeaveSeeder extends Seeder
{

    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();
        $admin = User::where('role', 'admin')->first();

        foreach ($employees as $employee) {
            Leave::create([
                'user_id' => $employee->id,
                'leave_type' => 'annual',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(12),
                'total_days' => 3,
                'reason' => 'Family vacation planned for the holidays.',
                'status' => 'pending',
            ]);


            Leave::create([
                'user_id' => $employee->id,
                'leave_type' => 'sick',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->subDays(13),
                'total_days' => 3,
                'reason' => 'Flu and fever, need rest to recover.',
                'status' => 'approved',
                'admin_comments' => 'Approved. Get well soon!',
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(16),
            ]);


            if ($employee->id % 2 == 0) {
                Leave::create([
                    'user_id' => $employee->id,
                    'leave_type' => 'personal',
                    'start_date' => Carbon::now()->addDays(5),
                    'end_date' => Carbon::now()->addDays(7),
                    'total_days' => 3,
                    'reason' => 'Personal matters to attend to.',
                    'status' => 'rejected',
                    'admin_comments' => 'Sorry, we have a critical project deadline during this period.',
                    'approved_by' => $admin->id,
                    'approved_at' => Carbon::now()->subDays(2),
                ]);
            }
        }


        $sampleLeaves = [
            [
                'leave_type' => 'maternity',
                'start_date' => Carbon::now()->addMonths(2),
                'end_date' => Carbon::now()->addMonths(2)->addDays(89),
                'total_days' => 90,
                'reason' => 'Maternity leave for upcoming delivery.',
                'status' => 'pending',
            ],
            [
                'leave_type' => 'emergency',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->subDays(4),
                'total_days' => 2,
                'reason' => 'Family emergency - grandmother hospitalized.',
                'status' => 'approved',
                'admin_comments' => 'Emergency approved. Hope everything is okay.',
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(6),
            ],
        ];

        foreach ($sampleLeaves as $leaveData) {
            $employee = $employees->random();
            Leave::create(array_merge($leaveData, [
                'user_id' => $employee->id,
            ]));
        }
    }
}