<?php
namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="GestionDuStock\Repository\OrdersMagasinRepository")
 */
class OrdersMagasin
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
    private $orderId;

    /**
     * @ORM\Column(type="integer")
     */
    private $magasinId;

    /**
     * @ORM\Column(type="integer")
     */
    private $productId;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;


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
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return OrdersMagasin
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMagasinId()
    {
        return $this->magasinId;
    }

    /**
     * @return OrdersMagasin
     */
    public function setMagasinId($magasinId)
    {
        $this->magasinId = $magasinId;
        return $this;
    }

    /**
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return OrdersMagasin
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
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
     * @return OrdersMagasin
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
        return $this;
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return OrdersMagasin
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

}