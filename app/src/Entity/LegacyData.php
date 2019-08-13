<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LegacyDataRepository")
 */
class LegacyData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="The email should not be blank.")
     * @Assert\Email()
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "The email cannot be longer than {{ limit }} characters."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\Length(
     *      max = 1000,
     *      maxMessage = "The message cannot be longer than {{ limit }} characters."
     * )
     */
    private $message;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return LegacyData
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return LegacyData
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
