<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface
{
    // constants
    const ROLE_USER = 'user';
    const ROLE_REP = 'rep';
    const ROLE_ADMIN = 'admin';

    const USER_TYPE_USER = 'user';
    const USER_TYPE_AGENT = 'agent';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(name="role", type="string", columnDefinition="enum('user', 'rep', 'admin')")
     */
    private $role;

    /**
     * @ORM\Column(name="balance", type="bigint", nullable=true)
     */
    private $balance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    // other properties and methods

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getRoles()
    {
        // define lowest level role
        $minRole = [self::ROLE_USER];

        if ($this->role === self::ROLE_REP) {
            return array_push($minRole, self::ROLE_REP);
        } elseif ($this->role === self::ROLE_ADMIN) {
            return array_push($minRole, self::ROLE_REP, self::ROLE_ADMIN);
        } else {
            return $minRole;
        }
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    // other methods, including security methods like getRoles()

    public function eraseCredentials() {}

    // ----- [static methods] ----- //
    /**
     * Returns default role per type(user/agent)
     * this function is necessary since "agent" is not an official role recorded in the database
     * 
     * @param false|string $type may be one of: user/agent
     */
    public static function resolveDefaultRoleForType($type = false) {
        return $type === self::USER_TYPE_AGENT ? self::ROLE_REP : self::ROLE_USER;
    }
}
