<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherDashboardController;
use \App\Http\Controllers\DuelController;

/* Genel Erişim (Herkese Açık) */

Route::get('/', function () {
    return view('welcome');
})->name('home');


/* Giriş Yapmış Kullanıcılar (Öğrenci, Öğretmen, Admin) */
Route::middleware(['auth'])->group(function () {

    // Varsayılan Dashboard rotasını Harita'ya yönlendiriyoruz
    Route::get('/dashboard', function () {
        return redirect()->route('map.index');
    })->name('dashboard');

    // 1. Konu Seçimi ve Yapay Zeka
    Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
    Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::post('/topics/{topic}/enroll', [TopicController::class, 'enroll'])->name('topics.enroll');

    // 2. Ana Harita
    Route::get('/map', [MapController::class, 'index'])->name('map.index');

    // 3. Bölge (Kartlar, Quiz, Monaco Editör)
    Route::get('/region/{region}', [RegionController::class, 'show'])->name('region.show');
    Route::post('/region/{region}/submit', [RegionController::class, 'submitAnswer'])->name('region.submit');

    // 4. Arena (Düello, Yetenek Ağacı ve Eşleştirme)
    Route::get('/arena', [\App\Http\Controllers\DuelController::class, 'index'])->name('arena.index');

    // Oyun Modları
    Route::post('/arena/bot-match', [\App\Http\Controllers\DuelController::class, 'botMatch'])->name('arena.botMatch');
    Route::post('/arena/random-match', [\App\Http\Controllers\DuelController::class, 'randomMatch'])->name('arena.randomMatch');
    Route::post('/arena/room/create', [\App\Http\Controllers\DuelController::class, 'createRoom'])->name('arena.createRoom');
    Route::post('/arena/room/join', [\App\Http\Controllers\DuelController::class, 'joinRoom'])->name('arena.joinRoom');

    // Savaş ve Kontrol
    Route::get('/arena/api/check-status/{duel}', [\App\Http\Controllers\DuelController::class, 'checkRoomStatus'])->name('arena.checkStatus');
    Route::get('/arena/battle/{duel}', [\App\Http\Controllers\DuelController::class, 'show'])->name('arena.show');
    Route::post('/arena/battle/{duel}/answer', [\App\Http\Controllers\DuelController::class, 'submitAnswer'])->name('arena.answer');

    /* Yetkili Ekranları (Role Middleware ile Korunuyor) */

    // Sadece Öğretmenler Girebilir
     Route::middleware(['role:teacher'])->group(function () {
        Route::get('/teacher', [\App\Http\Controllers\TeacherDashboardController::class, 'dashboard'])->name('teacher.dashboard');
        Route::post('/teacher/classroom', [\App\Http\Controllers\TeacherDashboardController::class, 'storeClassroom'])->name('teacher.storeClassroom');
        Route::post('/teacher/question', [\App\Http\Controllers\TeacherDashboardController::class, 'storeQuestion'])->name('teacher.storeQuestion');
        Route::post('/teacher/simulate', [\App\Http\Controllers\TeacherDashboardController::class, 'simulateTime'])->name('teacher.simulate');
    });

    // Öğrenciler için Sınıfa Katılma Rotası (Öğrenci grubunun içine ekle)
    Route::post('/student/join-class', [\App\Http\Controllers\TeacherDashboardController::class, 'joinClassroom'])->name('student.joinClassroom');

    // Sadece Adminler Girebilir
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/reports', function () {
            $reports = \App\Models\QuestionReport::with(['user', 'question.region'])->latest()->get();
            return view('admin.reports', compact('reports'));
        })->name('admin.reports');
    });
});

// Laravel Breeze Varsayılan Kimlik Doğrulama Rotaları
require __DIR__ . '/auth.php';
