<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="created_at_idx", columns={"created_at"}),
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\StatsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Stats
{
    use TimestampTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="stats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $video;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $view_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $like_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dislike_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $favorite_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comment_count;

    public function getId()
    {
        return $this->id;
    }

    public function getVideo(): Channel
    {
        return $this->video;
    }

    public function setVideo(Video $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getViewCount(): ?int
    {
        return $this->view_count;
    }

    public function setViewCount(?int $view_count): self
    {
        $this->view_count = $view_count;

        return $this;
    }

    public function getLikeCount(): ?int
    {
        return $this->like_count;
    }

    public function setLikeCount(?int $like_count): self
    {
        $this->like_count = $like_count;

        return $this;
    }

    public function getDislikeCount(): ?int
    {
        return $this->dislike_count;
    }

    public function setDislikeCount(?int $dislike_count): self
    {
        $this->dislike_count = $dislike_count;

        return $this;
    }

    public function getFavoriteCount(): ?int
    {
        return $this->favorite_count;
    }

    public function setFavoriteCount(?int $favorite_count): self
    {
        $this->favorite_count = $favorite_count;

        return $this;
    }

    public function getCommentCount(): ?int
    {
        return $this->comment_count;
    }

    public function setCommentCount(?int $comment_count): self
    {
        $this->comment_count = $comment_count;

        return $this;
    }
}
