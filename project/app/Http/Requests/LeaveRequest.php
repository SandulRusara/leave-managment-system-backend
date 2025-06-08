<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class LeaveRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leave_type' => ['required', Rule::in(['annual', 'sick', 'personal', 'maternity', 'paternity', 'emergency'])],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }


    public function messages(): array
    {
        return [
            'leave_type.required' => 'Please select a leave type.',
            'leave_type.in' => 'Please select a valid leave type.',
            'start_date.required' => 'Start date is required.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.required' => 'End date is required.',
            'end_date.after_or_equal' => 'End date must be on or after the start date.',
            'reason.required' => 'Please provide a reason for your leave.',
            'reason.min' => 'Reason must be at least 10 characters long.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->start_date && $this->end_date) {
                $startDate = Carbon::parse($this->start_date);
                $endDate = Carbon::parse($this->end_date);
                $totalDays = $startDate->diffInDays($endDate) + 1;


                if ($this->leave_type !== 'maternity' && $this->leave_type !== 'paternity' && $totalDays > 30) {
                    $validator->errors()->add('end_date', 'Leave duration cannot exceed 30 days for this leave type.');
                }


                if (auth()->check()) {
                    $overlappingLeaves = auth()->user()->leaves()
                        ->where(function ($query) use ($startDate, $endDate) {
                            $query->whereBetween('start_date', [$startDate, $endDate])
                                  ->orWhereBetween('end_date', [$startDate, $endDate])
                                  ->orWhere(function ($q) use ($startDate, $endDate) {
                                      $q->where('start_date', '<=', $startDate)
                                        ->where('end_date', '>=', $endDate);
                                  });
                        })
                        ->whereIn('status', ['pending', 'approved'])
                        ->count();

                    if ($overlappingLeaves > 0) {
                        $validator->errors()->add('start_date', 'You already have a leave request for these dates.');
                    }
                }
            }
        });
    }
}