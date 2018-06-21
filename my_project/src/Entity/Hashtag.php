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
    private $hastagName;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $readCount;

    public function getId()
    {
        return $this->id;
    }

    public function getHastagName(): ?string
    {
        return $this->hastagName;
    }

    public function setHastagName(string $hastagName): self
    {
        $this->hastagName = $hastagName;

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
}
