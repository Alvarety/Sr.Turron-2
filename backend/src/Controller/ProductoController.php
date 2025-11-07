<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Producto;
use App\Entity\Categoria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ProductoController extends AbstractController
{
    // =========================
    // Listar productos activos
    // =========================
    #[Route('/api/productos', name:'api_productos_list', methods:['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $productos = $em->getRepository(Producto::class)->findBy(['activo'=>true]);
        $data = [];

        foreach ($productos as $p) {
            $data[] = [
                'id' => $p->getId(),
                'nombre' => $p->getNombre(),
                'descripcion' => $p->getDescripcion(),
                'precio' => $p->getPrecio(),
                'stock' => $p->getStock(),
                'categoria' => $p->getCategoria()->getNombre(),
                'imagenUrl' => $p->getImagenUrl(),
                'activo' => $p->isActivo()
            ];
        }

        return $this->json($data);
    }

    // =========================
    // Ver un producto
    // =========================
    #[Route('/api/productos/{id}', name:'api_producto_detail', methods:['GET'])]
    public function detail(EntityManagerInterface $em, int $id): JsonResponse
    {
        $p = $em->getRepository(Producto::class)->find($id);
        if (!$p) return $this->json(['error'=>'Producto no encontrado'], 404);

        return $this->json([
            'id' => $p->getId(),
            'nombre' => $p->getNombre(),
            'descripcion' => $p->getDescripcion(),
            'precio' => $p->getPrecio(),
            'stock' => $p->getStock(),
            'categoria' => $p->getCategoria()->getNombre(),
            'imagenUrl' => $p->getImagenUrl(),
            'activo' => $p->isActivo()
        ]);
    }

    // =========================
    // Crear producto (admin)
    // =========================
    #[Route('/api/productos', name:'api_productos_create', methods:['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Buscar categoría por ID
        $categoria = $em->getRepository(Categoria::class)->find($data['categoria_id'] ?? null);
        if (!$categoria) {
            return $this->json(['error' => 'Categoría no encontrada'], 404);
        }

        $producto = new Producto();
        $producto->setNombre($data['nombre']);
        $producto->setDescripcion($data['descripcion'] ?? null);
        $producto->setPrecio($data['precio']);
        $producto->setStock($data['stock']);
        $producto->setCategoria($categoria);
        $producto->setImagenUrl($data['imagenUrl'] ?? null);
        $producto->setActivo($data['activo'] ?? true);

        $em->persist($producto);
        $em->flush();

        return $this->json([
            'message' => 'Producto creado',
            'producto' => [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'descripcion' => $producto->getDescripcion(),
                'precio' => $producto->getPrecio(),
                'stock' => $producto->getStock(),
                'categoria' => $producto->getCategoria()->getNombre(),
                'imagenUrl' => $producto->getImagenUrl(),
                'activo' => $producto->isActivo(),
            ]
        ]);
    }

    // =========================
    // Actualizar producto
    // =========================
    #[Route('/api/productos/{id}', name:'api_producto_update', methods:['PUT', 'PATCH'])]
    public function update(Request $request, EntityManagerInterface $em, int $id): JsonResponse
    {
        $producto = $em->getRepository(Producto::class)->find($id);
        if (!$producto) return $this->json(['error' => 'Producto no encontrado'], 404);

        $data = json_decode($request->getContent(), true);

        if (isset($data['nombre'])) $producto->setNombre($data['nombre']);
        if (isset($data['descripcion'])) $producto->setDescripcion($data['descripcion']);
        if (isset($data['precio'])) $producto->setPrecio($data['precio']);
        if (isset($data['stock'])) $producto->setStock($data['stock']);
        if (isset($data['imagenUrl'])) $producto->setImagenUrl($data['imagenUrl']);
        if (isset($data['activo'])) $producto->setActivo($data['activo']);

        // Buscar categoría por ID si se envía
        if (isset($data['categoria_id'])) {
            $categoria = $em->getRepository(Categoria::class)->find($data['categoria_id']);
            if (!$categoria) {
                return $this->json(['error' => 'Categoría no encontrada'], 404);
            }
            $producto->setCategoria($categoria);
        }

        $em->flush();

        return $this->json([
            'message' => 'Producto actualizado',
            'producto' => [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'descripcion' => $producto->getDescripcion(),
                'precio' => $producto->getPrecio(),
                'stock' => $producto->getStock(),
                'categoria' => $producto->getCategoria()->getNombre(),
                'imagenUrl' => $producto->getImagenUrl(),
                'activo' => $producto->isActivo(),
            ]
        ]);
    }

    // =========================
    // Eliminar producto
    // =========================
    #[Route('/api/productos/{id}', name:'api_producto_delete', methods:['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $producto = $em->getRepository(Producto::class)->find($id);
        if (!$producto) return $this->json(['error' => 'Producto no encontrado'], 404);

        $em->remove($producto);
        $em->flush();

        return $this->json(['message' => 'Producto eliminado']);
    }
}
