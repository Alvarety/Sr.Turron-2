<?php

namespace App\Controller;

use App\Entity\Pago;
use App\Entity\Pedido;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends AbstractController
{
    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function webhook(
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $endpointSecret = $_ENV['STRIPE_WEBHOOK_SECRET']; // ⚠️ Debes ponerlo en .env

        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\Exception $e) {
            return new Response('Firma inválida', 400);
        }

        // ========================
        // ⚠️ PAGO COMPLETADO
        // ========================
        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;

            // ID del pedido enviado desde React
            $pedidoId = $session->metadata->pedidoId;

            $pedido = $em->getRepository(Pedido::class)->find($pedidoId);

            if ($pedido) {

                // ===== Crear registro Pago =====
                $pago = new Pago();
                $pago->setPedido($pedido);
                $pago->setMetodoPago('tarjeta');  
                $pago->setMonto($session->amount_total / 100); // Stripe en centimos
                $pago->setEstadoPago('completado');
                $pago->setFechaPago(new \DateTime());

                $em->persist($pago);

                // ===== Cambiar estado del pedido =====
                $pedido->setEstado('pagado');
                $em->persist($pedido);

                $em->flush();
            }
        }

        return new Response('OK', 200);
    }
}
