<?php
namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $section_title = null;

    #[ORM\Column(length: 105)]
    private ?string $section_slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $section_detail = null;

    // Add a property for articles
    #[ORM\OneToMany(mappedBy: 'section', targetEntity: Article::class, cascade: ['persist', 'remove'])]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection(); // Initialize the collection
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getSectionTitle(): ?string
    {
        return $this->section_title;
    }

    public function setSectionTitle(string $section_title): static
    {
        $this->section_title = $section_title;
        return $this;
    }

    public function getSectionSlug(): ?string
    {
        return $this->section_slug;
    }

    public function setSectionSlug(string $section_slug): static
    {
        $this->section_slug = $section_slug;
        return $this;
    }

    public function getSectionDetail(): ?string
    {
        return $this->section_detail;
    }

    public function setSectionDetail(?string $section_detail): static
    {
        $this->section_detail = $section_detail;
        return $this;
    }

    // Add method to add an article
    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setSection($this); // Set the section for the article
        }
        return $this;
    }

    // Add method to get all articles
    public function getArticles(): Collection
    {
        return $this->articles;
    }
}
