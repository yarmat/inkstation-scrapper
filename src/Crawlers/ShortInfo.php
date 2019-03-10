<?php

namespace App\Crawlers;

use Symfony\Component\DomCrawler\Crawler;

class ShortInfo
{
    private $crawler;

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function get()
    {

        $items = $this->crawler->filter('ul.productListing li')
            ->each(function (Crawler $node, $i) {
                try {
                    $title = $this->btw($node->filter('span.text-small')->first()->text());

                    $oldPrice = '';
                    $price = $this->btw($node->filter('.price_amount')->first()->text());
                    $price = $this->removeTextFromString($price);
                    $price = explode('$', $price);
                    $price = $this->removeEmptyFromArray($price);
                    $price = array_values($price);

                    if (count($price) > 1) {
                        $oldPrice = $price[0];
                        $price = $price[1];
                    } else {
                        $price = $price[0];
                    }

                    $url = $node->filter('a')->first()->link()->getUri();

                    return [
                        'title'     => $title,
                        'price'     => $price,
                        'old_price' => $oldPrice,
                        'url'       => $url
                    ];

                } catch (\Exception $e) {
                    dump($e->getMessage());
                }
            });

        return array_values($items);

    }

    private function btw($b1)
    {
        $b1 = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $b1);
        $b1 = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $b1);
        return $b1;
    }

    private function removeEmptyFromArray(array $array)
    {
        return array_diff($array, array(''));
    }

    private function removeTextFromString($string)
    {
        return preg_replace("/[a-z]/i", "", $string);
    }

}