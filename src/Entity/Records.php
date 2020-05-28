<?php

namespace App\Entity;

use App\Repository\RecordsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecordsRepository::class)
 */
class Records
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $term;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $results;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datetime_created;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTerm(): ?string
    {
        return $this->term;
    }

    public function setTerm(?string $term): self
    {
        $this->term = $term;

        return $this;
    }

    public function getResults(): ?int
    {
        return $this->results;
    }

    public function setResults(?int $results): self
    {
        $this->results = $results;

        return $this;
    }

    public function getDatetimeCreated(): ?\DateTimeInterface
    {
        return $this->datetime_created;
    }

    public function setDatetimeCreated(?\DateTimeInterface $datetime_created): self
    {
        $this->datetime_created = $datetime_created;

        return $this;
    }
}
