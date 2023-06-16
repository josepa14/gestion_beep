<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Entity\Usuarios;
use App\Entity\Reservas;
use App\Repository\ProductosRepository;
use App\Repository\ReservasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ReservaController extends AbstractController
{

    #[Route('/reservas', name: 'reservas')]
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $keyword = $request->query->get('keyword');
        $categoria = $request->query->get('categoria');
        $subcategoria = $request->query->get('subcategoria');
        $precio = $request->query->get('precio');
        $marca = $request->query->get('marca');
        $productos = $doctrine->getRepository(Productos::class)->createQueryBuilder('p')
            ->where('p.stock > :stock ')
            ->setParameter('stock', 0);
            if (!empty($keyword)) {
                $productos->andWhere('p.nombre LIKE :keyword OR p.marca LIKE :keyword')
                      ->setParameter('keyword', '%'.$keyword.'%');
            }
        
            if (!empty($categoria)) {
                $productos->andWhere('p.categoria = :categoria')
                      ->setParameter('categoria', $categoria);
            }
            if (!empty($subcategoria)) {
                $productos->andWhere('p.subcategoria = :subcategoria')
                      ->setParameter('subcategoria', $subcategoria);
            }
        
            if (!empty($precio)) {
                $productos->andWhere('p.precio <= :precio')
                      ->setParameter('precio', $precio);
            }
            if (!empty($marca)) {
                $productos->andWhere('p.marca LIKE :marca')
                      ->setParameter('marca', $marca);
            }

           $productos->getQuery()
            ->getResult();

            $pagination = $paginator->paginate(
                $productos,
                $request->query->getInt('page', 1), // Obtén el número de página actual de la solicitud
                10 // Cantidad de elementos por página
            );
        
            return $this->render('reserva/index.html.twig', [
                'pagination' => $pagination,
            ]);

        // return $this->render('reserva/index.html.twig', [
        //     'productos' => $productos,
        // ]);
    }


    #[Route('/crear_reserva', name: 'crear_reserva')]
    public function crearReserva(Request $request, ManagerRegistry $doctrine, MailerInterface $mailer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $productoId = $request->request->get('productoId');
        $fechaReservaString = $request->request->get('fechaReserva', ''); //seguridad por si recibo una cadena vacia
        // Verifica si el usuario está autenticado
        if (!$this->getUser()) {
            return new JsonResponse(['success' => false, 'message' => 'Debes autenticarte para crear una reserva.']);
        }

        // Obtén el producto y el usuario autenticado
        $producto = $doctrine->getRepository(Productos::class)->find($productoId);
        $usuario = $this->getUser();
        //actualizamos el Stock
        $producto->setStock($producto->getStock() - 1);

        // Crea una nueva reserva
        $reserva = new Reservas();
        $reserva->setProducto($producto->getId());
        $reserva->setFechaReserva($fechaReservaString);
        $reserva->setEstado('pendiente de recogida');
        $reserva->setUser($usuario->getId());

        // Guarda la reserva en la base de datos
        $entityManager = $doctrine->getManager();
        $entityManager->persist($reserva);
        $entityManager->persist($producto);
        $entityManager->flush();

        //coreo de confirmacion de la reserva.
        $productoNombre = $producto->getNombre();
        $fechaReserva = $reserva->getFechaReserva();

        $email = (new Email())
            ->from($this->getParameter('correo_remitente'))
            ->to($usuario->getEmail())
            ->subject('Confirmación de reserva')
            ->html($this->renderView('reserva/confirmacion.html.twig', [
                'productoNombre' => $productoNombre,
                'fechaReserva' => $fechaReserva,
            ]));

        $mailer->send($email);
        return new JsonResponse(['success' => true, 'message' => 'Reserva creada exitosamente.']);
    }


    #[Route('/mis_reservas', name: 'mis_reservas')]
    public function misReservas(ReservasRepository $reservasRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $userId = $this->getUser()->getId();
        $reservas = $reservasRepository->findBy(['user' => $userId]);

        foreach ($reservas as $reserva) {
            $productoId = $reserva->getProducto();
            $producto = $entityManager->getRepository(Productos::class)->find($productoId);
            $reserva->setProducto($producto->getNombre());
        }


        return $this->render('reserva/mis_reservas.html.twig', [
            'reservas' => $reservas,
        ]);
    }



    #[Route('/cancelar_reserva', name: 'cancelar_reserva', methods: ['POST'])]
    public function cancelarReserva(Request $request, ReservasRepository $reservaRepository, ProductosRepository $productoRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $reservaId = $request->request->get('reservaId');
        $reserva = $reservaRepository->find($reservaId);

        if (!$reserva) {
            return new JsonResponse(['success' => false, 'message' => 'Reserva no encontrada.' . $reservaId]);
        }

        $productoId = $reserva->getProducto();
        $producto = $productoRepository->find($productoId);

        if (!$producto) {
            return new JsonResponse(['success' => false, 'message' => 'Producto no encontrado.']);
        }

        // Actualizar estado de la reserva
        $reserva->setEstado('cancelado');
        $entityManager->flush();

        // Incrementar el stock del producto
        $producto->setStock($producto->getStock() + 1);
        $entityManager->flush();
        $this->addFlash('reservaCancelada', 'La reserva ha sido cancelada exitosamente.');
        return new JsonResponse(['success' => true, 'message' => 'Reserva cancelada exitosamente.']);
    }



    #[Route('/gestionar_reservas', name: 'gestionar_reservas')]
    public function gestionarReservas(Request $request,EntityManagerInterface $entityManager, ReservasRepository $reservasRepository, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
    $reservas = $reservasRepository->findAll();

        foreach ($reservas as $reserva) {
            $productoId = $reserva->getProducto();
            $usuarioId = $reserva->getUser();
            $producto = $entityManager->getRepository(Productos::class)->find($productoId);
            $usuario = $entityManager->getRepository(Usuarios::class)->find($usuarioId);
            $reserva->setProducto($producto->getNombre());
            $reserva->setProducto($producto->getNombre());
            $reserva->setUser($usuario->getNombreCompleto()." ".$usuario->getTelefono());

        }

        $pagination = $paginator->paginate(
            $reservas,
            $request->query->getInt('page', 1),
            10 // Cantidad de elementos por página
        );
    
        return $this->render('reserva/gestionar_reservas.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/confirmar_venta', name: 'confirmar_venta', methods: ['POST'])]
    public function confirmarReserva(Request $request, ReservasRepository $reservaRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $reservaId = $request->request->get('reservaId');
        $reserva = $reservaRepository->find($reservaId);

        if (!$reserva) {
            return new JsonResponse(['success' => false, 'message' => 'Reserva no encontrada.']);
        }

        // Actualizar estado de la reserva a "comprada"
        $reserva->setEstado('comprado');
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Reserva confirmada exitosamente.']);
    }
}