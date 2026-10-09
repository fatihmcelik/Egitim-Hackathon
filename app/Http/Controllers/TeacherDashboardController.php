<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Classroom;
use App\Models\Question;
use App\Models\Region;

class TeacherDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $teacher = Auth::user();
        // Sınıfı, öğrencileri ve öğrencilerin yeteneklerini (skills) çekiyoruz
        $classroom = Classroom::with(['students.topics', 'students.skills'])->where('teacher_id', $teacher->id)->first();
        
        // Eğer sınıf var ama kodu yoksa anında üret
        if ($classroom && empty($classroom->code)) {
            $classroom->code = strtoupper(\Illuminate\Support\Str::random(6));
            $classroom->save(); 
        }
        
        $classLeaderboard = collect();
        $regions = Region::all(); 

        if ($classroom) {
            $classLeaderboard = $classroom->students->map(function ($student) {
                $student->total_xp = $student->skills->sum('pivot.score') + $student->topics->sum('pivot.xp');
                $student->homework_progress = rand(30, 100); 
                return $student;
            })->sortByDesc('total_xp')->values();
        }

        return view('teacher.dashboard', compact('classroom', 'classLeaderboard', 'regions'));
    }

    public function storeClassroom(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $code = strtoupper(Str::random(6));

        Classroom::create([
            'name' => $request->name,
            'teacher_id' => Auth::id(),
            'code' => $code,
        ]);

        return redirect()->back()->with('success', "Sınıf başarıyla oluşturuldu! Katılım Kodunuz: {$code}");
    }

    // YENİ: Çoklu Soru ve İçerik Ekleme Fonksiyonu
    public function storeQuestion(Request $request)
    {
        $classroom = Classroom::where('teacher_id', Auth::id())->first();
        $regionId = $request->input('region_id');

        // Formdan "questions" isimli bir dizi (array) geliyorsa hepsini döngüyle kaydet
        if ($request->has('questions')) {
            foreach ($request->questions as $q) {
                if (!empty($q['body']) && !empty($q['option_0'])) {
                    Question::create([
                        'region_id' => $regionId,
                        'classroom_id' => $classroom->id,
                        'body' => $q['body'],
                        'options' => json_encode([$q['option_0'], $q['option_1'], $q['option_2'], $q['option_3']]),
                        'correct_index' => $q['correct_index'] ?? 0,
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Tüm içerikler başarıyla sınıf haritasına eklendi!');
        }

        return redirect()->back()->with('error', 'Eklenecek geçerli bir içerik bulunamadı.');
    }

    public function joinClassroom(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $classroom = Classroom::where('code', strtoupper($request->code))->first();

        if (!$classroom) {
            return redirect()->back()->with('error', 'Geçersiz sınıf kodu.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['classroom_id' => $classroom->id]);

        return redirect()->back()->with('success', "{$classroom->name} sınıfına başarıyla katıldınız!");
    }
    
    public function refreshCode(Request $request)
    {
        $classroom = Classroom::where('teacher_id', Auth::id())->first();
        
        if ($classroom) {
            $classroom->code = strtoupper(\Illuminate\Support\Str::random(6));
            $classroom->save();
            return redirect()->back()->with('success', "Sınıf kodu başarıyla yenilendi! Yeni Kod: {$classroom->code}");
        }

        return redirect()->back()->with('error', 'Sınıf bulunamadı.');
    }
}