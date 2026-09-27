<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mahasiswas')->insert([
            [
                'id' => 11,
                'nim' => '230001',
                'nama' => 'Darma',
                'jurusan' => 'Teknologi Informasi',
                'angkatan' => 2023,
                'hobi' => 'I enjoy developing mobile applications and designing user interfaces',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'nim' => '230002',
                'nama' => 'Citra',
                'jurusan' => 'Teknologi Informasi',
                'angkatan' => 2023,
                'hobi' => 'I enjoy playing futsal, exercising, and doing outdoor activities',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'nim' => '230003',
                'nama' => 'Rizky',
                'jurusan' => 'Teknologi Informasi',
                'angkatan' => 2023,
                'hobi' => 'I enjoy reading novels, writing stories, and visiting libraries',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'nim' => '230004',
                'nama' => 'Komang',
                'jurusan' => 'Teknologi Informasi',
                'angkatan' => 2023,
                'hobi' => 'I enjoy cooking and trying different kinds of food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
