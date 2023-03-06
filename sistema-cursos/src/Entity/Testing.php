<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Repository\CursoRepository;
use App\Repository\JuegosRepository;
use App\Repository\TestingRepository;

#[ORM\Entity(repositoryClass: TestingRepository::class)]
class Testing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cursoId = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fe_creacion = null;


    public function __construct($idUsuario,$idJuego,$fecha){
        $this -> setcursoId($idJuego);
        $this -> setUserId($idUsuario);
        $this -> setFeCreacion($fecha);
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCursoId(): ?int
    {
        return $this->cursoId;
    }

    public function setCursoId(int $cursoId): self
    {
        $this->cursoId = $cursoId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getFeCreacion(): ?\DateTimeInterface
    {
        return $this->fe_creacion;
    }

    public function setFeCreacion(\DateTimeInterface $fe_creacion): self
    {
        $this->fe_creacion = $fe_creacion;

        return $this;
    }
    public function getCreador(UserRepository $userRepo, CursoRepository $cursoRepository ): User
    {
        $curso= $cursoRepository->findOneBy(["id"=>($this->cursoId)]);
        $userEncontrado = $userRepo->findOneBy(["id"=>($curso->getUserId())]);
         return $userEncontrado;
    }

    
}
