<?php

namespace AppBundle\Entity\Bowling;

use Bowling\Game as BaseGame;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Game extends BaseGame
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @Groups({"bowling"})
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
