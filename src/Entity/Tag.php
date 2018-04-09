<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="name_idx", columns={"name"}),
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
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

    public function getVideos()
    {
        return $this->videos;
    }
}
