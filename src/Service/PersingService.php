<?php
namespace App\Service;

use App\Controller\NewsController;


class PersingService
{   
    public $news;

    public function __construct(NewsController $news)
    {
        $this->news = $news;
    }
    
    public function getArray($url)
    {
        // $html = file_get_contents($url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $html = curl_exec($curl);
        // echo $html;
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//div[starts-with(@class, "lenta-item")]');
        $nodes_image = $xpath->query('//div[starts-with(@class, "lenta-image")]');
        // dd($nodes);
        $array = [];
        $array_images = [];
        foreach ($nodes as $key => $node) {
            // print_r($node->nodeValue);
            if($key == 0) {
                continue;
            }
            // echo "== Extracting data from node {$key} == <br>";
            $array[] = [
                'title' => $node->getElementsByTagName('h2')->item(0)->nodeValue?? "",
                'description' => $node->getElementsByTagName('p')->item(0)->nodeValue?? "",
                'date' => $node->getElementsByTagName('span')->item(1)->nodeValue?? "",
                'link' => $node->getElementsByTagName('a')->item(1)->getAttribute('href')?? "",
            ];
            // echo "==Data extracted== <br>";
        }
        
        foreach ($nodes_image as $node) {
            $array_images[] = [
                'image' => $node->getElementsByTagName('img')->item(1)->getAttribute('src')?? "",
            ];
        }

        // dd($array_images);

        foreach ($array as $key => $value) {
            $array[$key]['image'] = $array_images[$key]['image'];

            // call save news
            
            $this->news->save($array[$key]);
            

        }



        return $array;

        
    }
}