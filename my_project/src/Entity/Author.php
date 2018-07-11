<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author implements UserInterface
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=50, unique=True)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=True)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profilePicture;

    /**
     * @ORM\Column(type="string", length=72)
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = ['ROLE_AUTHOR'];


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @return mixed
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param mixed $bio
     */
    public function setBio($bio): void
    {
        $this->bio = $bio;
    }


    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    private $plainPassword;

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }


    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole($role)
    {
        if (in_array($role, $this->roles))
        {
            return;
        }

        $this->roles[] = $role;
    }



    public function removeRole($role)
    {   $index = array_search($role,$this->roles);

        if($index==false){
            return;
        }
        unset($this->roles[$index]);
        $this->roles=array_values($this->roles);
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }


}
