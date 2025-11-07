<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\Resena;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ComentarioController extends AbstractController
{
    #[Route('/api/comentarios', methods: ['POST'])]
    public function responder(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $usuario = $em->getRepository(Usuario::class)->find($data['usuario_id']);
        $resena = $em->getRepository(Resena::class)->find($data['resena_id']);

        if (!$usuario || !$resena) {
            return $this->json(['error' => 'Datos inválidos'], 400);
        }

        $comentario = new Comentario();
        $comentario->setUsuario($usuario);
        $comentario->setResena($resena);
        $comentario->setTexto($data['texto']);

        $em->persist($comentario);
        $em->flush();

        return $this->json(['message' => 'Comentario añadido correctamente']);
    }
}
