<?php

namespace App\Controller;

use App\Entity\Notas;
use App\Entity\Productos;
use App\Repository\NotasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnotacionesController extends AbstractController
{
    #[Route('/anotaciones', name: 'anotaciones')]
    public function index(NotasRepository $notasRepository): Response
    {
        $notas = $notasRepository->findAll();

        return $this->render('anotaciones/index.html.twig', [
            'anotaciones' => $notas,
        ]);
    }

    #[Route('/notas', name: 'notas')]
    public function mostrarNotas(NotasRepository $notasRepository): JsonResponse
    {
        $notas = $notasRepository->findAll();

        $notasArray = [];
        foreach ($notas as $nota) {
            $notasArray[] = [
                'id' => $nota->getId(),
                'titulo' => $nota->getTitulo(),
                'descripcion' => $nota->getDescripcion(),
            ];
        }

        return $this->json($notasArray);
    }
    #[Route('/guardar-nota', name: 'guardar_nota', methods: ['POST'])]
    public function guardarNota(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtener los datos enviados en la solicitud POST
        $titulo = $request->request->get('titulo');
        $descripcion = $request->request->get('descripcion');

        // Crear una nueva instancia de la entidad Nota y asignar los valores
        $nota = new Notas();
        $nota->setTitulo($titulo);
        $nota->setDescripcion($descripcion);

        // Guardar la nota en la base de datos
        $entityManager->persist($nota);
        $entityManager->flush();

        // Retornar una respuesta exitosa
        return $this->json(['success' => true]);
    }






    #[Route('/eliminar/{id}', name: 'eliminar_nota', methods: ['POST'])]
    public function eliminarNota($id, EntityManagerInterface $entityManager): Response
{
    $nota = $entityManager->getRepository(Notas::class)->find($id);

    if (!$nota) {
        return new Response('La nota no existe', 404);
    }
    $entityManager->remove($nota);
    $entityManager->flush();
    return new Response('La nota ha sido eliminada');
}









    #[Route('/productos-json', name: 'productos_json', methods: ['GET'])]
    public function getProductosJson(EntityManagerInterface $entityManager,)
    {
        // Obtén los datos de los productos desde la base de datos
        $productos = $entityManager->getRepository(Productos::class)->findAll();

        // Formatea los datos de los productos para devolverlos en formato JSON
        $productosArray = [];
        foreach ($productos as $producto) {
            $productosArray[] = [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'marca' => $producto->getMarca(),
                'categoria' => $producto->getCategoria(),
                'subcategoria' => $producto->getSubcategoria(),
                'stock' => $producto->getStock(),
                'estado' => $producto->getEstado(),
                'reservado' => $producto->getReservado(),
                'recogido' => $producto->getRecogido(),
                'precio' => $producto->getPrecio(),
            ];

        }
    return new JsonResponse($productosArray);
    }
}