<?php
namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="GestionDuStock\Repository\MagasinRepository")
 */
class Magasin
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="string", length=255)
    */
    private $nom;


    /**
    * @return integer
    */
    public function getId()
    {
        return $this->id;
    }

    /**
    * @return string
    */
    public function getNom()
    {
        return $this->nom;
    }

    /**
    * @return Magasin
    */
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

}