<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HashtagRepository")
 */
class Hashtag
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $hashtagName;


    /**
    * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="hashtags")
    * @ORM\JoinTable(name="post_hashtag")
     * */
    private $posts;

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }


    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $readCount;

    public function getId()
    {
        return $this->id;
    }

    public function getHashtagName(): ?string
    {
        return $this->hashtagName;
    }

    public function setHashtagName(string $hashtagName): self
    {
        $this->hashtagName = $hashtagName;

        return $this;
    }

    public function getReadCount(): ?int
    {
        return $this->readCount;
    }

    public function setReadCount(?int $readCount): self
    {
        $this->readCount = $readCount;

        return $this;
    }

    public function __toString()
    {
        return $this->getHashtagName();
    }


}
