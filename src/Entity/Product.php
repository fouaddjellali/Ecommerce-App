<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;
    #[ORM\Column(type: 'text')]
    private string $description;
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;
    #[ORM\Column(type: 'integer')]
    private int $stock;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $photo = null;
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    public function getPhoto(): ?string
    {
        return $this->photo;
    }
    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;
        return $this;
    }
}
