<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\Column(type="integer")
     */
    private $author_id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;
    /**
     * @ORM\Column(type="integer")
     */
    private $rate;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Episode", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $episode;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }
    public function setAuthorId(int $author_id): self
    {
        $this->author_id = $author_id;
        return $this;
    }
    public function getComment(): ?string
    {
        return $this->comment;
    }
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }
    public function getRate(): ?int
    {
        return $this->rate;
    }
    public function setRate(int $rate): self
    {
        $this->rate = $rate;
        return $this;
    }
    public function getEpisode(): ?Episode
    {
        return $this->episode;
    }
    public function setEpisode(?Episode $episode): self
    {
        $this->episode = $episode;
        return $this;
    }
}
