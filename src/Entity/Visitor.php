<?php

namespace App\Entity;

use App\Repository\VisitorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitorRepository::class)]
class Visitor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $visited_sites = null;

    #[ORM\Column(length: 255)]
    private ?string $first_visit = null;

    #[ORM\Column(length: 255)]
    private ?string $last_visit = null;

    #[ORM\Column(length: 255)]
    private ?string $browser = null;

    #[ORM\Column(length: 255)]
    private ?string $os = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 255)]
    private ?string $ip_address = null; 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisitedSites(): ?int
    {
        return $this->visited_sites;
    }

    public function setVisitedSites(int $visited_sites): static
    {
        $this->visited_sites = $visited_sites;

        return $this;
    }

    public function getFirstVisit(): ?string
    {
        return $this->first_visit;
    }

    public function setFirstVisit(string $first_visit): static
    {
        $this->first_visit = $first_visit;

        return $this;
    }

    public function getLastVisit(): ?string
    {
        return $this->last_visit;
    }

    public function setLastVisit(string $last_visit): static
    {
        $this->last_visit = $last_visit;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(string $browser): static
    {
        $this->browser = $browser;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(string $os): static
    {
        $this->os = $os;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): static
    {
        $this->ip_address = $ip_address;

        return $this;
    }
}
