<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Question;
use App\Models\CodingTask;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Morph Map ayarı
        Relation::enforceMorphMap([
            'question' => Question::class,
            'coding_task' => CodingTask::class,
        ]);
    }
}