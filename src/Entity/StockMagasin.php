<?php
namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="GestionDuStock\Repository\StockMagasinRepository")
 */
class StockMagasin
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="integer")
    */
    private $product;

    /**
    * @ORM\Column(type="integer")
    */
    private $magasin;
    
    /**
    * @ORM\Column(type="integer")
    */
    private $quantite;

    /**
    * @ORM\Column(type="string")
    */
    private $dateexpiration;
    
    
    /**
    * @return integer
    */
    public function getId()
    {
        return $this->id;
    }

    /**
    * @return integer
    */
    public function getProduct()
    {
        return $this->product;
    }

    /**
    * @return Magasin
    */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
    * @return integer
    */
    public function getMagasin()
    {
        return $this->magasin;
    }

    /**
    * @return Magasin
    */
    public function setMagasin($magasin)
    {
        $this->magasin = $magasin;
        return $this;
    }

    /**
    * @return integer
    */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
    * @return Magasin
    */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
        return $this;
    }

    /**
    * @return string
    */
    public function getDateexpiration()
    {
        return $this->dateexpiration;
    }

    /**
    * @return Magasin
    */
    public function setDateexpiration($dateexpiration)
    {
        $this->dateexpiration = $dateexpiration;
        return $this;
    }

}