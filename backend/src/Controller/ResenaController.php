<?php

namespace App\Controller;

use App\Entity\Resena;
use App\Entity\Producto;
use App\Entity\Usuario;
use App\Entity\Comentario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResenaController extends AbstractController
{
    // Obtener reseñas de un producto
    #[Route('/api/productos/{id}/resenas', methods: ['GET'])]
    public function getResenas(EntityManagerInterface $em, int $id): JsonResponse
    {
        $resenas = $em->getRepository(Resena::class)->findBy(['producto' => $id], ['fecha' => 'DESC']);
        $data = [];

        foreach ($resenas as $r) {
            $data[] = [
                'id' => $r->getId(),
                'usuario' => $r->getUsuario()->getNickname(),
                'comentario' => $r->getComentario(),
                'puntuacion' => $r->getPuntuacion(),
                'fecha' => $r->getFecha()->format('Y-m-d H:i'),
                'respuestas' => array_map(fn($c) => [
                    'usuario' => $c->getUsuario()->getNickname(),
                    'texto' => $c->getTexto(),
                    'fecha' => $c->getFecha()->format('Y-m-d H:i'),
                ], $r->getComentarios()->toArray())
            ];
        }

        return $this->json($data);
    }

    // Crear reseña nueva
    #[Route('/api/resenas', methods: ['POST'])]
    public function crearResena(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $usuario = $em->getRepository(Usuario::class)->find($data['usuario_id']);
        $producto = $em->getRepository(Producto::class)->find($data['producto_id']);

        if (!$usuario || !$producto) {
            return $this->json(['error' => 'Usuario o Producto inválido'], 400);
        }

        // Evitar reseña duplicada por usuario
        $existe = $em->getRepository(Resena::class)->findOneBy([
            'usuario' => $usuario,
            'producto' => $producto
        ]);

        if ($existe) {
            return $this->json(['error' => 'Ya has reseñado este producto'], 409);
        }

        $resena = new Resena();
        $resena->setUsuario($usuario);
        $resena->setProducto($producto);
        $resena->setComentario($data['comentario']);
        $resena->setPuntuacion($data['puntuacion']);

        $em->persist($resena);
        $em->flush();

        return $this->json(['message' => 'Reseña añadida correctamente']);
    }

    // Crear comentario en una reseña
    #[Route('/api/resenas/{id}/comentarios', methods: ['POST'])]
    public function comentarResena(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $usuario = $em->getRepository(Usuario::class)->find($data['usuario_id']);
        $resena = $em->getRepository(Resena::class)->find($id);

        if (!$usuario || !$resena) {
            return $this->json(['error' => 'Usuario o Reseña inválidos'], 400);
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
