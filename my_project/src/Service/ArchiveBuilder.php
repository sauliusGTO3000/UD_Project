<?php
/**
 * Created by PhpStorm.
 * User: SauliusGTO3000
 * Date: 7/19/2018
 * Time: 09:26
 */

namespace App\Service;

use App\Repository\PostRepository;

class ArchiveBuilder
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getArchiveData(){
        $calendar = [
            '01'=>'Sausis',
            '02'=>'Vasaris',
            '03'=>'Kovas',
            '04'=>'Balandis',
            '05'=>'Gegužė',
            '06'=>'Birželis',
            '07'=>'Liepa',
            '08'=>'Rugpjūtis',
            '09'=>'Rugsėjis',
            '10'=>'Spalis',
            '11'=>'Lapkritis',
            '12'=>'Gruodis'
            ];
        $yeartoPrint = "";
        $monthToPrint = "";
        $archiveOfPosts = [];
        $allPosts =  $this->postRepository->findPosted();
        foreach($allPosts as $post){
            $publishedDate = $post->getPublishDate();
            $publishedYear = $publishedDate->format('Y');
            if ($yeartoPrint != $publishedYear){
                $archiveOfPosts[$publishedYear]=[];
                $yeartoPrint = $publishedYear;
                $monthToPrint="";
            }
            $publishedMonth = $publishedDate->format('m');

            if ($monthToPrint != $publishedMonth){
                $archiveOfPosts[$publishedYear][$calendar[$publishedMonth]]=[];
                $monthToPrint = $publishedMonth;
            }
            $archiveOfPosts[$publishedYear][$calendar[$publishedMonth]][]='<a href="'.$post->getId().'">'.$post->getTitle().'</a>';
        }
        return($archiveOfPosts);
    }
}