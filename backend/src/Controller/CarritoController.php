<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Pedido;
use App\Entity\PedidoDetalle;
use App\Entity\Producto;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CarritoController extends AbstractController
{
    #[Route('/carrito', name: 'app_carrito')]
    public function index(): Response
    {
        return $this->render('carrito/index.html.twig', [
            'controller_name' => 'CarritoController',
        ]);
    }

    // =========================
    // Ver carrito de un usuario
    // =========================
    #[Route('/api/carrito/{usuarioId}', name:'api_carrito_ver', methods:['GET'])]
    public function verCarrito(EntityManagerInterface $em, int $usuarioId): JsonResponse
    {
        $usuario = $em->getRepository(Usuario::class)->find($usuarioId);
        if (!$usuario) return $this->json(['error'=>'Usuario no encontrado'],404);

        $pedido = $em->getRepository(Pedido::class)->findOneBy(['usuario'=>$usuario,'estado'=>'pendiente']);
        if (!$pedido) return $this->json(['message'=>'Carrito vacío'],200);

        $data = [];
        foreach ($pedido->getPedidoDetalles() as $detalle) {
            $data[] = [
                'producto' => $detalle->getProducto()->getNombre(),
                'cantidad' => $detalle->getCantidad(),
                'precio_unitario' => $detalle->getPrecioUnitario(),
                'subtotal' => $detalle->getSubtotal()
            ];
        }

        return $this->json([
            'pedido_id' => $pedido->getId(),
            'productos' => $data
        ]);
    }

    // =========================
    // Añadir producto al carrito
    // =========================
    #[Route('/api/carrito/add', name:'api_carrito_add', methods:['POST'])]
    public function addProducto(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $usuario = $em->getRepository(Usuario::class)->find($data['usuario_id']);
        $producto = $em->getRepository(Producto::class)->find($data['producto_id']);
        $cantidad = $data['cantidad'] ?? 1;

        if (!$usuario || !$producto) {
            return $this->json(['error'=>'Usuario o producto no encontrado'],404);
        }

        // Buscar carrito pendiente
        $pedido = $em->getRepository(Pedido::class)->findOneBy(['usuario'=>$usuario,'estado'=>'pendiente']);
        if (!$pedido) {
            $pedido = new Pedido();
            $pedido->setUsuario($usuario);
            $pedido->setEstado('pendiente');
            $em->persist($pedido);
        }

        $detalle = new PedidoDetalle();
        $detalle->setPedido($pedido);
        $detalle->setProducto($producto);
        $detalle->setCantidad($cantidad);

        $em->persist($detalle);
        $em->flush();

        return $this->json(['message'=>'Producto añadido al carrito','detalle_id'=>$detalle->getId()]);
    }

    // =========================
    // Checkout del carrito
    // =========================
    #[Route('/api/carrito/checkout', name:'api_carrito_checkout', methods:['POST'])]
    public function checkout(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usuario = $em->getRepository(Usuario::class)->find($data['usuario_id']);
        if (!$usuario) return $this->json(['error'=>'Usuario no encontrado'],404);

        $pedido = $em->getRepository(Pedido::class)->findOneBy(['usuario'=>$usuario,'estado'=>'pendiente']);
        if (!$pedido) return $this->json(['error'=>'Carrito vacío'],400);

        $total = 0;
        foreach ($pedido->getPedidoDetalles() as $detalle) {
            $total += $detalle->getSubtotal();
        }

        $pedido->setTotal($total);
        $pedido->setDireccionEnvio($data['direccion_envio'] ?? '');
        $pedido->setEstado('pendiente'); // o 'pagado' si ya se realiza el pago

        $em->flush();

        return $this->json([
            'message'=>'Pedido creado',
            'pedido_id'=>$pedido->getId(),
            'total'=>$total
        ]);
    }
}
