<?php

namespace App\Console\Commands;

use App\Models\MynaviUrl;
use Illuminate\Console\Command;
use Goutte;
use Carbon\Carbon;

class ScrapeMynavi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:mynavi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape mynavi';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->truncateTables();
        $this->saveUrls();
    }

    /**
     * truncate tables
     *
     * @return void
     */
    private function truncateTables()
    {
        MynaviUrl::truncate();
    }

    /**
     * save mynavi urls
     *
     * @return void
     */
    private function saveUrls()
    {
        foreach (range(1, 2) as $num) {
            $url = sprintf('https://tenshoku.mynavi.jp/list/pg%d/', $num);
            $crawler = Goutte::request('GET', $url);
            $urls = $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
                $href = $node->attr('href');
                return [
                    'url' => substr($href, 0, strpos($href, '/', 1) + 1),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            });
            MynaviUrl::insert($urls);
            sleep(30);
        }
    }
}
