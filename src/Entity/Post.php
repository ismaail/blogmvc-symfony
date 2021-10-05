<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Post
 *
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: \App\Repository\PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'text')]
    private string $content;

    /**
     * @Gedmo\Timestampable(on="create")
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Category::class, fetch: 'EAGER', inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class, fetch: 'EAGER', inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\OneToMany(mappedBy:'post', targetEntity: \App\Entity\Comment::class, orphanRemoval: true)]
    private $comments;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return \App\Entity\Post
     */
    public function setTitle(string $title): Post
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return \App\Entity\Post
     */
    public function setSlug(string $slug): Post
    {
        $this->slug = $slug;

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
     * @return \App\Entity\Post
     */
    public function setContent(string $content): Post
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
     * @return \App\Entity\Post
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): Post
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \App\Entity\Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param \App\Entity\Category $category
     *
     * @return \App\Entity\Post
     */
    public function setCategory(Category $category): Post
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return \App\Entity\User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param \App\Entity\User $author
     *
     * @return \App\Entity\Post
     */
    public function setAuthor(User $author): Post
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param \App\Entity\Comment $comment
     *
     * @return \App\Entity\Post
     */
    public function addComment(Comment $comment): Post
    {
        if (! $this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    /**
     * @param \App\Entity\Comment $comment
     *
     * @return \App\Entity\Post
     */
    public function removeComment(Comment $comment): Post
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }
}
