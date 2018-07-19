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

    public function getArchiveData($maxResults = null){
        $counter =0;
        $calendar = [
            '01'=>'sausis',
            '02'=>'vasaris',
            '03'=>'kovas',
            '04'=>'balandis',
            '05'=>'gegužė',
            '06'=>'birželis',
            '07'=>'liepa',
            '08'=>'rugpjūtis',
            '09'=>'rugsėjis',
            '10'=>'spalis',
            '11'=>'lapkritis',
            '12'=>'gruodis'
            ];
        $yeartoPrint = "";
        $monthToPrint = "";
        $archiveOfPosts = [];
        $allPosts =  $this->postRepository->findPosted($maxResults);
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