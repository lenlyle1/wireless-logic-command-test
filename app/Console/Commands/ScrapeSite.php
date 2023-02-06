<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\SiteScraper;

class ScrapeSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape-site';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape site and make json array';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'https://wltest.dns-systems.net/';

        $this->line('Loading Site');
        $siteScraper =  new SiteScraper($url);

        $this->line('Scraping Packages and Sorting');
        $packagesJSON = $siteScraper->scrapePackages($url);
        $this->line("Outputting JSON:\n");
        $this->line($packagesJSON);

        return Command::SUCCESS;
    }
}
