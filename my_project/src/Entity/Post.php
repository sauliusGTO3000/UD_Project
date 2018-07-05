<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $posted;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author")
     *
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverImage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="text")
     */
    private $shortContent;

    /**
     * @return mixed
     */
    public function getShortContent()
    {
        return $this->shortContent;
    }

    /**
     * @param mixed $shortContent
     */
    public function setShortContent($shortContent): void
    {
        $this->shortContent = $shortContent;
    }

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Hashtag")
     */
    private $hashtags;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $readCount;

    public function getId()
    {
        return $this->id;
    }

    public function getPosted(): ?bool
    {
        return $this->posted;
    }

    public function setPosted(bool $posted): self
    {
        $this->posted = $posted;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getPublishDate(): ?\DateTimeInterface
    {
        return $this->publishDate;
    }

    public function setPublishDate(?\DateTimeInterface $publishDate): self
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    public function getAuthor():?Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHashtags()
    {
        return $this->hashtags;
    }

    public function addHashtag($hashtag){
        $this->hashtags[]=$hashtag;
    }

    public function removeHashtag($hashtag){
        $this->hashtags->removeElement($hashtag);
    }

    public function getReadCount(): ?int
    {
        return $this->readCount ;
    }

    public function setReadCount(?int $readCount): self
    {
        $this->readCount = $readCount;

        return $this;
    }

    public function __construct()
    {
        $this->publishDate=new \DateTime();
        $this->hashtags= new ArrayCollection();
    }
}
