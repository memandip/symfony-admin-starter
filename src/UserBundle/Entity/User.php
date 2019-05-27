<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @var ArrayCollection|Group[]
     *
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\Group", inversedBy="users", cascade={"persist" ,"remove"})
     * @ORM\JoinTable(name="user_groups_relation",
     *      joinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={ @ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $userGroups;

    public function __construct()
    {
        parent::__construct();
        $this->roles = ['ROLE_ADMIN'];
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFullName(){
        return $this->firstName . " ". $this->lastName;
    }

    /**
     * @return ArrayCollection|Group[]
     */
    public function getUserGroups()
    {
        return $this->userGroups;
    }

    /**
     * @param ArrayCollection|Group[] $userGroups
     */
    public function setUserGroups($userGroups)
    {
        $this->userGroups = $userGroups;
    }

    /**
     * @param GroupInterface $userGroup
     *
     * @return $this
     */
    public function addGroup(GroupInterface $userGroup)
    {
        if (!$this->getUserGroups()->contains($userGroup)) {
            $this->getUserGroups()->add($userGroup);
            $userGroup->addUser($this);
        }

        return $this;
    }

    /**
     * @param GroupInterface $userGroup
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $userGroup)
    {
        if ($this->getUserGroups()->contains($userGroup)) {
            $this->getUserGroups()->removeElement($userGroup);
            $userGroup->removeUser($this);
        }

        return $this;
    }

}

