<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Topic;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([SoftwareTopicSeeder::class]);

        // 1. ADMİN HESABI
        User::create([
            'name' => 'Sistem Yöneticisi',
            'email' => 'admin@edu.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. ÖĞRETMEN HESABI
        $teacher = User::create([
            'name' => 'Fatih Hoca (Mentor)',
            'email' => 'mentor@edu.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // 3. SINIF OLUŞTURMA
        $classroom = Classroom::create([
            'name' => 'Hackathon Sınıfı',
            'teacher_id' => $teacher->id,
        ]);

        // 4. ÖĞRENCİ HESAPLARI
        $student1 = User::create([
            'name' => 'Ayşe Demir',
            'email' => 'ayse@edu.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'classroom_id' => $classroom->id,
        ]);

        $student2 = User::create([
            'name' => 'Burak Şahin',
            'email' => 'burak@edu.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'classroom_id' => $classroom->id,
        ]);

        // Öğrencilere örnek ilerleme verelim
        $topic = Topic::first();
        $regions = Region::where('topic_id', $topic->id)->orderBy('order')->get();

        if($regions->count() >= 2) {
            // Ayşe çok çalışkan, yeni çözmüş
            $student1->topics()->attach($topic->id, ['xp' => 450, 'current_title_id' => 2]);
            $student1->regions()->attach($regions[0]->id, ['status' => 'completed', 'last_reviewed_at' => Carbon::now()]);
            $student1->regions()->attach($regions[1]->id, ['status' => 'open']);

            // Burak tembel, 4 gün önce çözmüş (Sis efekti için)
            $student2->topics()->attach($topic->id, ['xp' => 120, 'current_title_id' => 1]);
            $student2->regions()->attach($regions[0]->id, ['status' => 'completed', 'last_reviewed_at' => Carbon::now()->subDays(4)]);
        }
    }
}