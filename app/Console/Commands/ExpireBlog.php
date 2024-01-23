<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ExpireBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-blog {hours=3 : The number of hours after which posts and comments will be expired}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire blog posts and comments. By default posts and comments older than 3 hours will be expired.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $validator = $this->validateArguments();

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        $this->info("Expiring blog posts and comments which are older than {$this->argument('hours')} hours...");
        $posts = BlogPost::withCount('comments')->where('created_at', '<', now()->subHours($this->argument('hours')))->get();

        $this->withProgressBar($posts, function (BlogPost $post) {
            $post->delete();
            $post->comments()->delete();
        });

        $this->newLine(2);
        $this->info('Expired '.$posts->count().' blog posts and '.$posts->sum('comments_count').' comments.');
    }

    private function validateArguments(): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make(['hours' => $this->argument('hours')], [
            'hours' => 'integer|min:1',
        ]);
    }
}
