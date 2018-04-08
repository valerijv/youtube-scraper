<?php

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="video_id_idx", columns={"video_id"}),
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Video
{
    use TimestampTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $channel;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    private $videoId;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    private $publishedAt;

    private $score;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="videos")
     */
    private $tags;

    public function __construct() {
        $this->tags = new ArrayCollection();
    }

    public function addTag(Tag $tag): self
    {
        $tag->addVideo($this);
        $this->tags[] = $tag;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function setChannel(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    public function setVideoId(string $videoId): self
    {
        $this->videoId = $videoId;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("published")
     */
    public function getPublished()
    {
        return Carbon::instance($this->getPublishedAt())->diffForHumans();
    }
    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("score")
     */
    public function getScore()
    {
        return $this->score;
    }


    public function setScore($score): self
    {
        $this->score = $score;

        return $this;
    }
}
