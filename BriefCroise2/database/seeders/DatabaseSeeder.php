<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Colocation;
use App\Models\Membership;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'       => 'Admin EasyColoc',
            'email'      => 'admin@easycoloc.fr',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'reputation' => 3,
        ]);

        $alice = User::create([
            'name'       => 'Alice Martin',
            'email'      => 'alice@example.com',
            'password'   => Hash::make('password'),
            'reputation' => 1,
        ]);

        $bob = User::create([
            'name'       => 'Bob Dupont',
            'email'      => 'bob@example.com',
            'password'   => Hash::make('password'),
            'reputation' => 0,
        ]);

        $claire = User::create([
            'name'       => 'Claire Petit',
            'email'      => 'claire@example.com',
            'password'   => Hash::make('password'),
            'reputation' => -1,
        ]);

        $colocation = Colocation::create([
            'name'     => 'Appart Belleville',
            'owner_id' => $alice->id,
            'status'   => 'active',
        ]);

        Membership::create([
            'user_id'       => $alice->id,
            'colocation_id' => $colocation->id,
            'role'          => 'owner',
            'joined_at'     => Carbon::now()->subMonths(3),
        ]);

        Membership::create([
            'user_id'       => $bob->id,
            'colocation_id' => $colocation->id,
            'role'          => 'member',
            'joined_at'     => Carbon::now()->subMonths(2),
        ]);

        Membership::create([
            'user_id'       => $claire->id,
            'colocation_id' => $colocation->id,
            'role'          => 'member',
            'joined_at'     => Carbon::now()->subMonth(),
        ]);

        $courses   = Category::create(['colocation_id' => $colocation->id, 'name' => 'Courses']);
        $loyer     = Category::create(['colocation_id' => $colocation->id, 'name' => 'Loyer']);
        $internet  = Category::create(['colocation_id' => $colocation->id, 'name' => 'Internet']);
        $loisirs   = Category::create(['colocation_id' => $colocation->id, 'name' => 'Loisirs']);

        Expense::create([
            'colocation_id' => $colocation->id,
            'payer_id'      => $alice->id,
            'category_id'   => $loyer->id,
            'title'         => 'Loyer Janvier',
            'amount'        => 1200.00,
            'date'          => Carbon::now()->subMonth()->startOfMonth(),
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'payer_id'      => $bob->id,
            'category_id'   => $courses->id,
            'title'         => 'Courses Carrefour',
            'amount'        => 85.50,
            'date'          => Carbon::now()->subWeeks(2),
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'payer_id'      => $alice->id,
            'category_id'   => $internet->id,
            'title'         => 'Abonnement Internet',
            'amount'        => 29.99,
            'date'          => Carbon::now()->subWeek(),
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'payer_id'      => $claire->id,
            'category_id'   => $loisirs->id,
            'title'         => 'Soirée Pizza',
            'amount'        => 45.00,
            'date'          => Carbon::now()->subDays(3),
        ]);
    }
}