<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 150,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dataCreation = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(
        'today',
        message: "La date d'échéance ne peut pas être dans le passé."
    )]
    private ?\DateTimeImmutable $dataEcheance = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'La priorité est obligatoire.')]
    #[Assert\Choice(
        choices: ['basse', 'moyenne', 'haute', 'urgente'],
        message: 'Priorité invalide.'
    )]
    private ?string $priorite = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Choice(
        choices: ['a_faire', 'en_cours', 'terminee'],
        message: 'Statut invalide.'
    )]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    private ?User $assigneA = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDataCreation(): ?\DateTimeImmutable
    {
        return $this->dataCreation;
    }

    public function setDataCreation(\DateTimeImmutable $dataCreation): static
    {
        $this->dataCreation = $dataCreation;
        return $this;
    }

    public function getDataEcheance(): ?\DateTimeImmutable
    {
        return $this->dataEcheance;
    }

    public function setDataEcheance(?\DateTimeImmutable $dataEcheance): static
    {
        $this->dataEcheance = $dataEcheance;
        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): static
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getAssigneA(): ?User
    {
        return $this->assigneA;
    }

    public function setAssigneA(?User $assigneA): static
    {
        $this->assigneA = $assigneA;
        return $this;
    }
}