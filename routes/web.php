<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\DuelController;
use App\Http\Controllers\StudySessionController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

/* Genel Erişim (Herkese Açık) */
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('welcome');
})->name('home');


/* Giriş Yapmış Kullanıcılar (Öğrenci, Öğretmen, Admin) */
Route::middleware(['auth'])->group(function () {

    // Ana Karşılama Ekranı (HomeController veriyi hazırlar: $user, $topic, $progress ...)
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Profil Sayfası (Yetenek barları ve XP detayları)
    Route::get('/profile', fn () => view('profile.show', ['user' => Auth::user()]))->name('profile.show');

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
    Route::get('/arena', [DuelController::class, 'index'])->name('arena.index');

    // Oyun Modları
    Route::post('/arena/bot-match', [DuelController::class, 'botMatch'])->name('arena.botMatch');
    Route::post('/arena/random-match', [DuelController::class, 'randomMatch'])->name('arena.randomMatch');
    Route::post('/arena/room/create', [DuelController::class, 'createRoom'])->name('arena.createRoom');
    Route::post('/arena/room/join', [DuelController::class, 'joinRoom'])->name('arena.joinRoom');

    // Savaş ve Kontrol
    Route::get('/arena/api/check-status/{duel}', [DuelController::class, 'checkRoomStatus'])->name('arena.checkStatus');
    Route::get('/arena/battle/{duel}', [DuelController::class, 'show'])->name('arena.show');
    Route::post('/arena/battle/{duel}/answer', [DuelController::class, 'submitAnswer'])->name('arena.answer');

    // Çalışma Oturumu (Study Session) Planlama Rotası
    Route::post('/study-session/request', [StudySessionController::class, 'store'])->name('study-session.store');

    /* Yetkili Ekranları (Role Middleware ile Korunuyor) */

    // Sadece Öğretmenler Girebilir
    Route::middleware(['role:teacher'])->group(function () {
        Route::get('/teacher', [TeacherDashboardController::class, 'dashboard'])->name('teacher.dashboard');
        Route::post('/teacher/classroom', [TeacherDashboardController::class, 'storeClassroom'])->name('teacher.storeClassroom');
        Route::post('/teacher/question', [TeacherDashboardController::class, 'storeQuestion'])->name('teacher.storeQuestion');
        Route::post('/teacher/simulate', [TeacherDashboardController::class, 'simulateTime'])->name('teacher.simulate');
        Route::post('/teacher/refresh-code', [TeacherDashboardController::class, 'refreshCode'])->name('teacher.refreshCode');
    });

    // Öğrenciler için Sınıfa Katılma Rotası
    Route::post('/student/join-class', [TeacherDashboardController::class, 'joinClassroom'])->name('student.joinClassroom');

    // Sadece Adminler Girebilir
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/reports', function () {
            $reports = \App\Models\QuestionReport::with(['user', 'question.region'])->latest()->get();
            return view('admin.reports', compact('reports'));
        })->name('admin.reports');
    });
});


require __DIR__ . '/auth.php';