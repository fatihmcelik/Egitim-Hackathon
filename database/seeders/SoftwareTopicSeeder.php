<?php

namespace Database\Seeders;

use App\Models\Topic;
use App\Models\Title;
use App\Models\Region;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\CodingTask;
use Illuminate\Database\Seeder;

class SoftwareTopicSeeder extends Seeder
{
    public function run(): void
    {
        $data = require database_path('data/software_topic.php');

        // 1. Topic (Konu) Güncelle veya Oluştur
        $topic = Topic::updateOrCreate(
            ['slug' => $data['topic']['slug']],
            [
                'name' => $data['topic']['name'],
                'description' => $data['topic']['description'],
                'is_ai_generated' => false,
            ]
        );

        // 2. Unvanları Temizle ve Yeniden Ekle
        Title::where('topic_id', $topic->id)->delete();
        foreach ($data['titles'] as $titleData) {
            Title::create(array_merge($titleData, ['topic_id' => $topic->id]));
        }

        // 3. Bölgeler ve İçerikleri
        $previousRegionId = null;
        $order = 1;

        foreach ($data['regions'] as $regionData) {
            // Bölgeyi güncelle veya oluştur
            $region = Region::updateOrCreate(
                ['slug' => $regionData['slug'], 'topic_id' => $topic->id],
                [
                    'name' => $regionData['name'],
                    'order' => $order++,
                    'x' => $regionData['x'],
                    'y' => $regionData['y'],
                    'icon' => $regionData['icon'],
                    'description' => $regionData['description'],
                    'prerequisite_region_id' => $previousRegionId,
                    'is_ai_generated' => false,
                ]
            );

            $previousRegionId = $region->id; // Sonraki bölge için önkoşul olarak kaydet

            // Alt içerikleri temizle (Yumuşak silme kullanıyorsak forceDelete)
            Lesson::where('region_id', $region->id)->delete();
            Question::where('region_id', $region->id)->forceDelete();
            CodingTask::where('region_id', $region->id)->forceDelete();

            // Öğretici Kartlar
            if (isset($regionData['lessons'])) {
                foreach ($regionData['lessons'] as $lessonData) {
                    Lesson::create(array_merge($lessonData, ['region_id' => $region->id]));
                }
            }

            // Test Soruları
            if (isset($regionData['questions'])) {
                foreach ($regionData['questions'] as $questionData) {
                    Question::create(array_merge($questionData, [
                        'region_id' => $region->id,
                        'is_approved' => true,
                        'is_ai_generated' => false,
                    ]));
                }
            }

            // Kod Görevleri
            if (isset($regionData['coding_tasks'])) {
                foreach ($regionData['coding_tasks'] as $taskData) {
                    CodingTask::create([
                        'region_id' => $region->id,
                        'title' => $taskData['title'],
                        'description' => $taskData['description'],
                        'starter_code' => $taskData['starter_code'],
                        'hint' => $taskData['hint'],
                        'test_cases' => $taskData['test_cases'], // Casts sayesinde dizi olarak verilebilir
                        'is_approved' => true,
                        'is_ai_generated' => false,
                    ]);
                }
            }
        }
    }
}