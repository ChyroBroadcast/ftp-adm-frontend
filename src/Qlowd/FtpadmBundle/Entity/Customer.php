<?php

namespace Qlowd\FtpadmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="Customer", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name", "url"})})
 * @ORM\Entity
 */
class Customer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_space", type="bigint", nullable=false)
     */
    private $totalSpace;

    /**
     * @var integer
     *
     * @ORM\Column(name="used_space", type="bigint", nullable=false)
     */
    private $usedSpace;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=0, nullable=false)
     */
    private $price;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Qlowd\FtpadmBundle\Entity\Address", inversedBy="customer")
     * @ORM\JoinTable(name="addresscustomerrelation",
     *   joinColumns={
     *     @ORM\JoinColumn(name="customer", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="address", referencedColumnName="id")
     *   }
     * )
     */
    private $address;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->address = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Customer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set totalSpace
     *
     * @param integer $totalSpace
     * @return Customer
     */
    public function setTotalSpace($totalSpace)
    {
        $this->totalSpace = $totalSpace;

        return $this;
    }

    /**
     * Get totalSpace
     *
     * @return integer
     */
    public function getTotalSpace()
    {
        return $this->totalSpace;
    }

    /**
     * Set usedSpace
     *
     * @param integer $usedSpace
     * @return Customer
     */
    public function setUsedSpace($usedSpace)
    {
        $this->usedSpace = $usedSpace;

        return $this;
    }

    /**
     * Get usedSpace
     *
     * @return integer
     */
    public function getUsedSpace()
    {
        return $this->usedSpace;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Customer
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Add address
     *
     * @param \Qlowd\FtpadmBundle\Entity\Address $address
     * @return Customer
     */
    public function addAddress(\Qlowd\FtpadmBundle\Entity\Address $address)
    {
        $this->address[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \Qlowd\FtpadmBundle\Entity\Address $address
     */
    public function removeAddress(\Qlowd\FtpadmBundle\Entity\Address $address)
    {
        $this->address->removeElement($address);
    }

    /**
     * Get address
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddress()
    {
        return $this->address;
    }
    /**
     * @var string
     */
    private $path;


    /**
     * Set path
     *
     * @param string $path
     * @return Customer
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * @var string
     */
    private $url;


    /**
     * Set url
     *
     * @param string $url
     * @return Customer
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
