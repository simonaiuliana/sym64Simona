<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column(length: 160)]
    private ?string $title = null;

    #[ORM\Column(length: 162)]
    private ?string $title_slug = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $article_data_create = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $article_date_posted = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $published = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitleSlug(): ?string
    {
        return $this->title_slug;
    }

    public function setTitleSlug(string $title_slug): static
    {
        $this->title_slug = $title_slug;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getArticleDataCreate(): ?\DateTimeInterface
    {
        return $this->article_data_create;
    }

    public function setArticleDataCreate(\DateTimeInterface $article_data_create): static
    {
        $this->article_data_create = $article_data_create;

        return $this;
    }

    public function getArticleDatePosted(): ?\DateTimeInterface
    {
        return $this->article_date_posted;
    }

    public function setArticleDatePosted(?\DateTimeInterface $article_date_posted): static
    {
        $this->article_date_posted = $article_date_posted;

        return $this;
    }

    public function getPublished(): ?int
    {
        return $this->published;
    }

    public function setPublished(int $published): static
    {
        $this->published = $published;

        return $this;
    }
}
