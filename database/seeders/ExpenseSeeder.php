<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\CashRegister;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $categories = ExpenseCategory::all();
        $users = User::all();
        // Assuming CashRegister table may or may not be empty
        $cashRegisters = CashRegister::all();

        if ($categories->isEmpty() || $users->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 40; $i++) {
            $category = $categories->random();
            $user = $users->random();
            $cashRegister = $cashRegisters->isEmpty() ? null : $cashRegisters->random();
            
            Expense::create([
                'reference_no' => 'EXP-' . strtoupper($faker->bothify('????-####')),
                'expense_category_id' => $category->id,
                'user_id' => $user->id,
                'cash_register_id' => $cashRegister ? $cashRegister->id : null,
                'amount' => $faker->randomFloat(2, 50, 2000),
                'note' => $faker->sentence(),
                'created_at' => Carbon::now()->subDays(rand(1, 90)),
            ]);
        }
    }
}
