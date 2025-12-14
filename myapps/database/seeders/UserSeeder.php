<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'c14210034@john.petra.ac.id',
                'password' => '$2y$12$Txf4RAhmkET1OLVqTDRkEO1zkVM7BuO6ukJuadqBPRVM7ugIOQufW',
                'remember_token' => 'ZmOFoE8Tt81OGvOCOCeeQD0Kle8fKXHbA2LhvyHqmqVFyglqnf0zqyjDkiEs',
                'created_at' => '2025-10-31 00:01:01',
                'updated_at' => '2025-12-14 11:13:36',
            ],
            [
                'id' => 2,
                'name' => 'joy fisca',
                'username' => 'joy',
                'email' => 'joy@example.com',
                'password' => '$2y$12$vbZdvz.jRaRUEDrnzwjQ..2yOrQ8p8N02znrj88DSBfP0J23NE7.a',
                'remember_token' => null,
                'created_at' => '2025-11-04 03:27:15',
                'updated_at' => '2025-11-04 03:27:15',
            ],
            [
                'id' => 3,
                'name' => 'rey',
                'username' => 'rey',
                'email' => 'rey@example.com',
                'password' => '$2y$12$8MWERo6iZygWwpDx3e/WR.wvvfUas8bW/qxW04YxiuSwhAMI/k6KS',
                'remember_token' => null,
                'created_at' => '2025-11-10 17:13:07',
                'updated_at' => '2025-11-10 17:13:07',
            ],
            [
                'id' => 4,
                'name' => 'jerry',
                'username' => 'jerry',
                'email' => 'jerry@example.com',
                'password' => '$2y$12$8J7mCCBlC9cFQiOwmtiG/OQuUqXAOyBHSLKbZPmqK2XjBtfsVqLme',
                'remember_token' => null,
                'created_at' => '2025-11-10 17:34:19',
                'updated_at' => '2025-11-10 17:34:19',
            ],
            [
                'id' => 5,
                'name' => 'yanto',
                'username' => 'yanto',
                'email' => 'yanto@example.com',
                'password' => '$2y$12$bYU.kWAN30HnzjcRCQyJFuucdz3tdHQytG1IlYUIHFtkLDvSt6WkK',
                'remember_token' => 'oqIDf0AlfzLQJ6xwn9ojK0CuGGQ5kZBoDJoPIK5moSovb7X8hDv5MMW4w6jk',
                'created_at' => '2025-11-18 03:02:05',
                'updated_at' => '2025-11-18 03:02:05',
            ],
        ];

        foreach ($data as $item) {
            User::updateOrCreate(
                ['id' => $item['id']],
                $item
            );
        }

        $this->command->info('User berhasil di-seed!');
    }
}
