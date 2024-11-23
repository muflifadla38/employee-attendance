<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'dob' => '2011-11-11',
                'city' => 'Jogja',
                'user_id' => 2,
            ],
            [
                'dob' => '2012-12-12',
                'city' => 'Bantul',
                'user_id' => 3,
            ],
            [
                'dob' => '2010-10-10',
                'city' => 'Sleman',
                'user_id' => 4,
            ],
            [
                'dob' => '2010-10-10',
                'city' => 'Sleman',
                'user_id' => 5,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
