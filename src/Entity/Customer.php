<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'customers')]
class Customer extends User
{
    #[ORM\OneToOne(targetEntity: LoyaltyCard::class, mappedBy: 'customer', cascade: ['persist', 'remove'])]
    private ?LoyaltyCard $loyaltyCard = null;

    public function getLoyaltyCard(): ?LoyaltyCard
    {
        return $this->loyaltyCard;
    }

    public function setLoyaltyCard(LoyaltyCard $loyaltyCard): self
    {
        $this->loyaltyCard = $loyaltyCard;
        return $this;
    }
}
