<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Form\ProductosType;
use App\Repository\ProductosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/productos')]
class ProductosController extends AbstractController
{
    #[Route('/', name: 'app_productos', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, ProductosRepository $productosRepository): Response
{
    $this->denyAccessUnlessGranted('ROLE_WORKER');
    $query = $productosRepository->createQueryBuilder('p')
        ->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // Número de página, se toma del parámetro "page" en la URL
        10 // Cantidad de elementos por página
    );

    return $this->render('productos/index.html.twig', [
        'pagination' => $pagination,
    ]);
}

    #[Route('/new', name: 'app_productos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductosRepository $productosRepository,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_WORKER');
        $producto = new Productos();
        $form = $this->createForm(ProductosType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagenFile = $form->get('imagen')->getData();

            if ($imagenFile) {
                $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();

                try {
                    $imagenFile->move(
                        $this->getParameter('imagenes_directorio'),
                        $newFilename
                    );
                    $producto->setImagen($newFilename);
                } catch (FileException $e) {
                }
            }

            // Persistir y guardar el producto en la base de datos
            $entityManager->persist($producto);
            $entityManager->flush();

        return $this->redirectToRoute('app_productos');
    }

        return $this->renderForm('productos/new.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_productos_show', methods: ['GET'])]
    public function show(Productos $producto): Response
    {
        $this->denyAccessUnlessGranted('ROLE_WORKER');
        return $this->render('productos/show.html.twig', [
            'producto' => $producto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_productos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Productos $producto, ProductosRepository $productosRepository, EntityManagerInterface $entityManager): Response
{

     $this->denyAccessUnlessGranted('ROLE_WORKER');
    $form = $this->createForm(ProductosType::class, $producto);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imagenFile = $form->get('imagen')->getData();

        if ($imagenFile) {
            // Eliminar la imagen anterior
            $oldImagen = $producto->getImagen();
            if ($oldImagen) {
                $oldImagePath = $this->getParameter('imagenes_directorio') . '/' . $oldImagen;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imagenBase64 = base64_encode(file_get_contents($imagenFile));
            $nuevoNombreImagen = uniqid() . '.' . $imagenFile->guessExtension();
            $imagenFile->move($this->getParameter('imagenes_directorio'), $nuevoNombreImagen);

            // Asignar la nueva imagen al producto
            $producto->setImagen($nuevoNombreImagen);
        }

        // Guardar los cambios en la base de datos
        $entityManager->flush();
        return $this->redirectToRoute('app_productos', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('productos/edit.html.twig', [
        'producto' => $producto,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_productos_delete', methods: ['POST'])]
    public function delete(Request $request, Productos $producto, ProductosRepository $productosRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_WORKER');
        if ($this->isCsrfTokenValid('delete'.$producto->getId(), $request->request->get('_token'))) {
            $productosRepository->remove($producto, true);
        }

        return $this->redirectToRoute('app_productos', [], Response::HTTP_SEE_OTHER);
    }
}
