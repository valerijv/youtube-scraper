<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="name_idx", columns={"name"}),
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Video", inversedBy="tags")
     */
    private $videos;

    public function __construct() {
        $this->videos = new ArrayCollection();
    }

    public function addVideo(Video $video): ?self
    {
        if ($this->videos->contains($video)) {
            return null;
        }

        $this->videos[] = $video;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
