<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'alice',
                'email' => 'alice@mail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('123456'),
                'remember_token' => Str::random(10),
                'updated_by' => null,
                'created_at' => Carbon::now()

            ],
            [
                'name' => 'bob',
                'email' => 'bob@mail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('123456'),
                'remember_token' => Str::random(10),
                'updated_by' => null,
                'created_at' => Carbon::now()
            ]
        ];
        foreach ($users as $key => $user) {
            $find = User::where('email', $user['email'])->first();
            if ($find) {
                $upd = User::findOrFail($find->id);
                $upd->update($user);
            } else {
                User::create($user);
            }
        }
        // User::insert($user);
    }
}
