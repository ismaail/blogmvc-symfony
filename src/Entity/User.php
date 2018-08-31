<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class User
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @const string
     */
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="author")
     */
    private $posts;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return \App\Entity\User
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return \App\Entity\User
     */
    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @param \App\Entity\Post $post
     *
     * @return \App\Entity\User
     */
    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param \App\Entity\Post $post
     *
     * @return \App\Entity\User
     */
    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return \App\Entity\User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
