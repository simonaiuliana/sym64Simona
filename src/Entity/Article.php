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
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null; // Relație cu User

    #[ORM\Column(length: 160)]
    private ?string $title = null;

    #[ORM\Column(length: 162, unique: true)]
    private ?string $title_slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $article_date_create = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $article_date_posted = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private ?int $published = 0;

    #[ORM\ManyToOne(targetEntity: Section::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Section $section = null; // Relație cu Section

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user; // Returnează User
    }

    public function setUser(?User $user): static
    {
        $this->user = $user; // Setează User
        return $this;
    }

    // Rămânând restul metodelor...
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

    public function getArticleDateCreate(): ?\DateTimeInterface
    {
        return $this->article_date_create;
    }

    public function setArticleDateCreate(\DateTimeInterface $article_date_create): static
    {
        $this->article_date_create = $article_date_create;
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

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): static
    {
        $this->section = $section; // Setează secțiunea
        return $this;
    }
}

