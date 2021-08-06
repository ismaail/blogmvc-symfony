<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Comment
 *
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: \App\Repository\CommentRepository::class)]
#[ORM\EntityListeners([\App\Entity\CommentListener::class])]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private string $email;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Post::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private Post $post;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3)]
    private string $content;

    /**
     * @Gedmo\Timestampable(on="create")
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return \App\Entity\Comment
     */
    public function setUsername(string $username): Comment
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return \App\Entity\Comment
     */
    public function setEmail(string $email): Comment
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \App\Entity\Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param \App\Entity\Post $post
     *
     * @return \App\Entity\Comment
     */
    public function setPost(Post $post): Comment
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return \App\Entity\Comment
     */
    public function setContent(string $content): Comment
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     *
     * @return \App\Entity\Comment
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): Comment
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     *
     * @return \App\Entity\Comment
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): Comment
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getGravatar(): string
    {
        $hash = md5($this->email);

        return "https://www.gravatar.com/avatar/$hash?d=mm&s=100";
    }
}
