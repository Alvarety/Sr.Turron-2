<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Pago;
use App\Entity\Pedido;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class PagoController extends AbstractController
{
    #[Route('/pago', name: 'app_pago')]
    public function index(): Response
    {
        return $this->render('pago/index.html.twig', [
            'controller_name' => 'PagoController',
        ]);
    }

    // =========================
    // Registrar un pago
    // =========================
    #[Route('/api/pagos', name:'api_pagos_create', methods:['POST'])]
    public function crearPago(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pedido = $em->getRepository(Pedido::class)->find($data['pedido_id']);

        if (!$pedido) {
            return $this->json(['error'=>'Pedido no encontrado'], 404);
        }

        $pago = new Pago();
        $pago->setPedido($pedido);
        $pago->setMetodoPago($data['metodo_pago']);
        $pago->setMonto($data['monto']);
        $pago->setEstadoPago($data['estado_pago'] ?? 'pendiente');

        $em->persist($pago);
        $em->flush();

        return $this->json([
            'message'=>'Pago registrado',
            'pago_id'=>$pago->getId(),
            'estado_pago'=>$pago->getEstadoPago()
        ]);
    }

    // =========================
    // Listar pagos de un pedido
    // =========================
    #[Route('/api/pagos/pedido/{pedidoId}', name:'api_pagos_por_pedido', methods:['GET'])]
    public function pagosPorPedido(EntityManagerInterface $em, int $pedidoId): JsonResponse
    {
        $pedido = $em->getRepository(Pedido::class)->find($pedidoId);
        if (!$pedido) return $this->json(['error'=>'Pedido no encontrado'], 404);

        $pagos = $pedido->getPagos();
        $data = [];
        foreach ($pagos as $pago) {
            $data[] = [
                'id' => $pago->getId(),
                'metodo_pago' => $pago->getMetodoPago(),
                'monto' => $pago->getMonto(),
                'fecha_pago' => $pago->getFechaPago()->format('Y-m-d H:i:s'),
                'estado_pago' => $pago->getEstadoPago()
            ];
        }

        return $this->json($data);
    }
}
