<?php

namespace App\Controller;

use App\Entity\Categoria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class CategoriaController extends AbstractController
{
    // =========================
    // Listar todas las categorías
    // =========================
    #[Route('/api/categorias', name: 'api_categorias_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $categorias = $em->getRepository(Categoria::class)->findAll();
        $data = [];

        foreach ($categorias as $c) {
            $data[] = [
                'id' => $c->getId(),
                'nombre' => $c->getNombre(),
                'descripcion' => $c->getDescripcion(),
            ];
        }

        return $this->json($data);
    }

    // =========================
    // Ver una categoría y sus productos
    // =========================
    #[Route('/api/categorias/{id}', name: 'api_categoria_detail', methods: ['GET'])]
    public function detail(EntityManagerInterface $em, int $id): JsonResponse
    {
        $categoria = $em->getRepository(Categoria::class)->find($id);
        if (!$categoria) {
            return $this->json(['error' => 'Categoría no encontrada'], 404);
        }

        // Cargar productos de esta categoría
        $productos = [];
        foreach ($categoria->getProductos() as $p) {
            $productos[] = [
                'id' => $p->getId(),
                'nombre' => $p->getNombre(),
                'precio' => $p->getPrecio(),
                'imagenUrl' => $p->getImagenUrl(),
            ];
        }

        return $this->json([
            'id' => $categoria->getId(),
            'nombre' => $categoria->getNombre(),
            'descripcion' => $categoria->getDescripcion(),
            'productos' => $productos,
        ]);
    }
}
