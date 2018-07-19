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
        $yeartoPrint = "";
        $monthToPrint = "";
        $allPosts =  $this->postRepository->findPosted();
        foreach($allPosts as $post){
            $publishedDate = $post->getPublishDate();
            $publishedYear = $publishedDate->format('Y');
            while ($yeartoPrint != $publishedYear){
                echo '<div class = "publishedYear">'.$publishedDate->format('Y');
                $yeartoPrint = $publishedYear;
                echo "<br>";
            }
            $publishedMonth = $publishedDate->format('m');
            while ($monthToPrint != $publishedMonth){
                echo '<div class = "publishedYear">'.$publishedDate->format('m');
                $monthToPrint = $publishedMonth;
                echo "<br>";
            }
            echo '<a href="'.$post->getId().'">'.$post->getTitle().'</a>';
            echo "<br>";
            echo "</div>";
            echo "</div>";
        }

    }


}