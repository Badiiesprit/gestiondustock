<?php

namespace GestionDuStock\Repository;

use Doctrine\ORM\EntityRepository;
use GestionDuStock\Traits\RepositoryTrait;


class StockMagasinRepository extends EntityRepository
{
    use RepositoryTrait;

    /**
     * Récupère les enregistrements de StockMagasin dont la date d'expiration est dans les 3 prochains jours.
     *
     * @return StockMagasin[]|null
     */
    public function findExpiringStock(): ?array
    {
        $expirationDateLimit = new \DateTime('+10 days');

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT sm FROM PrestaShopBundle\Entity\StockMagasin sm WHERE sm.dateexpiration <= :expirationDate AND sm.dateexpiration !='' "
        )
            ->setParameter('expirationDate', $expirationDateLimit);

        return $query->getResult();
    }
}