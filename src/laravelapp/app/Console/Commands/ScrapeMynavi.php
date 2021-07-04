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
        $url = 'https://tenshoku.mynavi.jp/list/pg33/';
        $crawler = Goutte::request('GET', $url);
        $urls = $crawler->filter('.cassetteRecruit__copy > a')->each(function ($node) {
            return [
                'url' => $node->attr('href'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            // dump(substr($href, 0, strpos($href, '/', 1) + 1));
        });
        // dd($urls);
        MynaviUrl::insert($urls);


    }
}
