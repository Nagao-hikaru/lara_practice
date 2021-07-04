<?php

namespace App\Console\Commands;

use App\Models\MynaviUrl;
use App\Models\MynaviJob;
use Illuminate\Console\Command;
use Goutte;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

class ScrapeMynavi extends Command
{
    const HOST = 'https://tenshoku.mynavi.jp';
    const FILE_PATH = 'app/mynavi_jobs.csv';
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
        // $this->truncateTables();
        // $this->saveUrls();
        // $this->saveJobs();
        $this->exportCsv();
    }

    /**
     * truncate tables
     *
     * @return void
     */
    private function truncateTables()
    {
        MynaviUrl::truncate();
        MynaviJob    ::truncate();
    }

    /**
     * save mynavi urls
     *
     * @return void
     */
    private function saveUrls()
    {
        foreach (range(1, 1) as $num) {
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
            // sleep(30);
        }
    }

    /**
     * save mynavi Jobs
     *
     * @return void
     */
    private function saveJobs()
    {
        $mynavi_urls = MynaviUrl::all();
        foreach (MynaviUrl::all() as $mynavi_url) {
            $url = $this::HOST . $mynavi_url->url;
            $crawler = Goutte::request('GET', $url);

            MynaviJob::create([
                'url' => $url,
                'title' => $this->getTitle($crawler),
                'company_name' => $this->getCompanyName($crawler),
                'features' => $this->getFeatures($crawler),
            ]);
            break;
            sleep(30);
        }
    }

    /**
     * get title
     *
     * @param Crawler $crawler
     * @return string
     */
    private function getTitle(Crawler $crawler)
    {
        return $crawler->filter('.occName')->text();
    }

    /**
     * get company name
     *
     * @param Crawler $crawler
     * @return string
     */
    private function getCompanyName(Crawler $crawler)
    {
        return $crawler->filter('.companyName')->text();
    }

    /**
     * get features
     *
     * @param Crawler $crawler
     * @return string
     */
    private function getFeatures(Crawler $crawler)
    {
        $features =  $crawler->filter('.cassetteRecruit__attribute.cassetteRecruit__attribute-jobinfo .cassetteRecruit__attributeLabel > span')
            ->each(function ($node) {
                return $node->text();
            });
        return implode(',', $features);
    }

    /**
     * export csv
     *
     * @return string
     */
    private function exportCsv()
    {
        fopen(storage_path($this::FILE_PATH), 'w');
    }
}
