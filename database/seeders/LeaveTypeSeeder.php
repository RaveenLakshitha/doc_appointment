<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name'              => 'Annual Leave',
                'code'              => 'AL',
                'description'       => 'Paid leave for vacation or personal reasons',
                'days_allowed'      => 14,
                'is_paid'           => true,
                'requires_approval' => true,
                'active'            => true,
            ],
            [
                'name'              => 'Sick Leave',
                'code'              => 'SL',
                'description'       => 'Paid leave for illness or medical appointments',
                'days_allowed'      => 7,
                'is_paid'           => true,
                'requires_approval' => false,
                'active'            => true,
            ],
            [
                'name'              => 'Casual Leave',
                'code'              => 'CL',
                'description'       => 'Short-term paid leave for urgent personal matters',
                'days_allowed'      => 7,
                'is_paid'           => true,
                'requires_approval' => true,
                'active'            => true,
            ],
            [
                'name'              => 'Maternity Leave',
                'code'              => 'ML',
                'description'       => 'Paid leave for childbirth and newborn care',
                'days_allowed'      => 84, // 12 weeks standard in many countries
                'is_paid'           => true,
                'requires_approval' => true,
                'active'            => true,
            ],
            [
                'name'              => 'Paternity Leave',
                'code'              => 'PL',
                'description'       => 'Paid leave for fathers after childbirth',
                'days_allowed'      => 14,
                'is_paid'           => true,
                'requires_approval' => true,
                'active'            => true,
            ],
            [
                'name'              => 'Unpaid Leave',
                'code'              => 'UL',
                'description'       => 'Leave without pay for extended personal reasons',
                'days_allowed'      => 0, // no default entitlement
                'is_paid'           => false,
                'requires_approval' => true,
                'active'            => true,
            ],
            [
                'name'              => 'Bereavement Leave',
                'code'              => 'BL',
                'description'       => 'Paid leave for loss of immediate family member',
                'days_allowed'      => 3,
                'is_paid'           => true,
                'requires_approval' => true,
                'active'            => true,
            ],
            [
                'name'              => 'Study / Training Leave',
                'code'              => 'STL',
                'description'       => 'Leave for professional development or exams',
                'days_allowed'      => 5,
                'is_paid'           => true,
                'requires_approval' => true,
                'active'            => false, // inactive by default
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate(
                ['code' => $type['code']], // unique by code
                $type
            );
        }
    }
}