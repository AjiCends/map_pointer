<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        $programs = [
            [
                'name' => 'Program Pengembangan Masyarakat Desa',
                'description' => 'Program ini bertujuan untuk meningkatkan kesejahteraan masyarakat desa melalui berbagai kegiatan pemberdayaan ekonomi dan sosial. Meliputi pelatihan keterampilan, pengembangan UMKM, dan pembangunan infrastruktur dasar.',
            ],
            [
                'name' => 'Program Edukasi Lingkungan',
                'description' => 'Program kesadaran lingkungan yang meliputi kampanye penghijauan, pengelolaan sampah, dan edukasi tentang pentingnya menjaga kelestarian alam. Program ini menargetkan sekolah-sekolah dan masyarakat umum.',
            ],
            [
                'name' => 'Program Kesehatan Masyarakat',
                'description' => 'Program yang berfokus pada peningkatan derajat kesehatan masyarakat melalui penyuluhan kesehatan, pemeriksaan gratis, imunisasi, dan pembangunan fasilitas kesehatan dasar di daerah terpencil.',
            ],
            [
                'name' => 'Program Literasi Digital',
                'description' => 'Program untuk meningkatkan kemampuan digital masyarakat, khususnya generasi muda dan pelaku UMKM. Meliputi pelatihan penggunaan teknologi, e-commerce, dan pemasaran digital.',
            ]
        ];
        
        foreach ($programs as $program) {
            Program::create([
                'user_id' => $user->id,
                'name' => $program['name'],
                'description' => $program['description'],
            ]);
        }
    }
}
