<?php

namespace App\Entity;

use App\Repository\ProductosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductosRepository::class)]
class Productos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $marca = "otro";

    #[ORM\Column(length: 255)]
    private ?string $categoria = "otro";

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subcategoria = "otro";

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = null;

    #[ORM\Column(length: 255)]
    private ?string $reservado = "no";

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recogido = "no";

    #[ORM\Column(length: 5000, nullable: true, options:["default"=>"sin imagen.jpg"])]
    private ?string $imagen = null;

    #[ORM\Column(length: 255)]
    private ?int $precio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getSubcategoria(): ?string
    {
        return $this->subcategoria;
    }

    public function setSubcategoria(?string $subcategoria): self
    {
        $this->subcategoria = $subcategoria;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getReservado(): ?string
    {
        return $this->reservado;
    }

    public function setReservado(string $reservado): self
    {
        $this->reservado = $reservado;

        return $this;
    }

    public function getRecogido(): ?string
    {
        return $this->recogido;
    }

    public function setRecogido(?string $recogido): self
    {
        $this->recogido = $recogido;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getPrecio(): ?int
    {
        return $this->precio;
    }

    public function setPrecio(int $precio): self
    {
        $this->precio = $precio;

        return $this;
    }
}
