<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateDatasetJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $basePath = base_path('ai');
        exec("cd $basePath && python update_dataset.py");
        exec("cd $basePath && python build_embeddings.py");
        exec("cd $basePath && python train_model.py");
    }
}