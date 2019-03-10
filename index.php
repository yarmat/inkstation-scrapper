<?php
define('BASE_PATH', realpath(dirname(__FILE__)));

require BASE_PATH . '/vendor/autoload.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Crawlers\ShortInfo;


$url = 'https://www.inkstation.com.au/search';

$client = new Client();

$crawler = $client->request('GET', $url);

$pagesNumber = $crawler->filter('a.page-link')
    ->each(function (Crawler $node, $i) {
        return preg_replace("/[^0-9]/", '', $node->link()->getUri());
    });

asort($pagesNumber);

$maxPageNumber = array_pop($pagesNumber);

$items = new ShortInfo($crawler);
$items = $items->get();

for ($i = 2; $i <= 5; $i++) {
    sleep(3);

    $mod_url = $url . '?page=' . $i;

    $crawler = $client->request('GET', $mod_url);

    $crawler = new ShortInfo($crawler);

    $items = array_merge($items, $crawler->get());
}

dd($items);

