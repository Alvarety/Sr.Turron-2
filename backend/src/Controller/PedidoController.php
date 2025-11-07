<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Pedido;
use App\Entity\Usuario;
use App\Entity\Producto;
use App\Entity\PedidoDetalle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class PedidoController extends AbstractController
{
    // =========================
    // ✅ Listar pedidos
    // =========================
    #[Route('/api/pedidos', name:'api_pedidos_listar', methods:['GET'])]
    public function listarPedidos(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $usuarioId = $request->query->get('usuario_id');
        $usuarioRepo = $em->getRepository(Usuario::class);
        $pedidoRepo = $em->getRepository(Pedido::class);

        // Si no viene usuario_id, devolver error
        if (!$usuarioId) {
            return $this->json(['error' => 'usuario_id no enviado'], 400);
        }

        $usuario = $usuarioRepo->find($usuarioId);
        if (!$usuario) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        // 🔹 Si es admin, ver todos los pedidos
        if ($usuario->getRol() === 'admin') {
            $pedidos = $pedidoRepo->findAll();
        } else {
            // 🔹 Si es cliente, solo sus pedidos
            $pedidos = $pedidoRepo->findBy(['usuario' => $usuario]);
        }

        $data = array_map(fn(Pedido $pedido) => [
            'id' => $pedido->getId(),
            'fecha' => $pedido->getFechapedido()->format('Y-m-d H:i:s'),
            'estado' => $pedido->getEstado(),
            'total' => $pedido->getTotal(),
            'direccion_envio' => $pedido->getDireccionEnvio(),
            'usuario' => [
                'id' => $pedido->getUsuario()->getId(),
                'nombre' => $pedido->getUsuario()->getNombre(),
                'email' => $pedido->getUsuario()->getEmail(),
            ],
        ], $pedidos);

        return $this->json($data);
    }

    // =========================
    // Crear nuevo pedido
    // =========================
    #[Route('/api/pedidos', name:'api_pedidos_crear', methods:['POST'])]
    public function crearPedido(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['usuario_id']) || !isset($data['productos'])) {
            return $this->json(['error' => 'Datos incompletos'], 400);
        }

        $usuario = $em->getRepository(Usuario::class)->find($data['usuario_id']);
        if (!$usuario) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        $pedido = new Pedido();
        $pedido->setUsuario($usuario);
        $pedido->setEstado('pendiente');
        $pedido->setFechapedido(new \DateTime());

        $total = 0;
        $erroresStock = [];

        foreach ($data['productos'] as $prodData) {
            $producto = $em->getRepository(Producto::class)->find($prodData['id']);
            if (!$producto) continue;

            $cantidad = (int)($prodData['cantidad'] ?? 1);
            $precioUnitario = $producto->getPrecio();

            // 🧩 Validar stock disponible
            if ($producto->getStock() < $cantidad) {
                $erroresStock[] = [
                    'producto' => $producto->getNombre(),
                    'disponible' => $producto->getStock(),
                    'solicitado' => $cantidad
                ];
                continue; // No añadir al pedido si no hay stock suficiente
            }

            $subtotal = $precioUnitario * $cantidad;
            $total += $subtotal;

            // Crear detalle del pedido
            $detalle = new PedidoDetalle();
            $detalle->setPedido($pedido);
            $detalle->setProducto($producto);
            $detalle->setCantidad($cantidad);
            $detalle->setPrecioUnitario($precioUnitario);
            $detalle->setSubtotal($subtotal);

            // 🧮 Actualizar stock del producto
            $producto->setStock($producto->getStock() - $cantidad);

            $em->persist($detalle);
            $em->persist($producto);
        }

        // Si hubo errores de stock, abortar el pedido
        if (!empty($erroresStock)) {
            return $this->json([
                'error' => 'Algunos productos no tienen stock suficiente',
                'detalles' => $erroresStock,
            ], 400);
        }

        $pedido->setTotal($total);
        $pedido->setDireccionEnvio($data['direccion_envio'] ?? '');

        $em->persist($pedido);
        $em->flush();

        return $this->json([
            'message' => '✅ Pedido creado correctamente',
            'pedido_id' => $pedido->getId(),
            'total' => $total,
        ]);
    }

    // =========================
    // Modificar pedido
    // =========================
    #[Route('/api/pedidos/{id}', name:'api_pedidos_modificar', methods:['PUT'])]
    public function modificarPedido(EntityManagerInterface $em, Request $request, int $id): JsonResponse
    {
        $pedido = $em->getRepository(Pedido::class)->find($id);
        if (!$pedido) {
            return $this->json(['error'=>'Pedido no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['estado'])) $pedido->setEstado($data['estado']);
        if (isset($data['total'])) $pedido->setTotal($data['total']);
        if (isset($data['direccion_envio'])) $pedido->setDireccionEnvio($data['direccion_envio']);

        $em->flush();

        return $this->json([
            'message' => 'Pedido actualizado correctamente',
            'pedido_id' => $pedido->getId(),
        ]);
    }

    // =========================
    // Eliminar pedido
    // =========================
    #[Route('/api/pedidos/{id}', name:'api_pedidos_eliminar', methods:['DELETE'])]
    public function eliminarPedido(EntityManagerInterface $em, int $id): JsonResponse
    {
        $pedido = $em->getRepository(Pedido::class)->find($id);
        if (!$pedido) {
            return $this->json(['error'=>'Pedido no encontrado'], 404);
        }

        $em->remove($pedido);
        $em->flush();

        return $this->json([
            'message' => 'Pedido eliminado correctamente',
            'pedido_id' => $id,
        ]);
    }
}
