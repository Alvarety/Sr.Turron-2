<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class UsuarioController extends AbstractController
{
    // ============================
    // Registro de usuarios
    // ============================
    #[Route('/api/users/register', name:'api_register', methods:['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $usuario = new Usuario();
        $usuario->setNombre($data['nombre']);
        $usuario->setApellido1($data['apellido1'] ?? null);
        $usuario->setApellido2($data['apellido2'] ?? null);
        $usuario->setNickname($data['nickname'] ?? null);
        $usuario->setEmail($data['email']);
        $usuario->setTelefono($data['telefono'] ?? null);
        $usuario->setDireccion($data['direccion'] ?? null);
        $usuario->setFotoPerfil($data['fotoPerfil'] ?? null);
        $usuario->setRol('cliente');

        $hashedPassword = $passwordHasher->hashPassword($usuario, $data['password']);
        $usuario->setContrasenaHash($hashedPassword);

        try {
            $em->persist($usuario);
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(['error'=>'El email ya está registrado'], 400);
        }

        return $this->json([
            'message'=>'Usuario registrado',
            'usuario'=>[
                'id'=>$usuario->getId(),
                'nombre'=>$usuario->getNombre(),
                'apellido1'=>$usuario->getApellido1(),
                'apellido2'=>$usuario->getApellido2(),
                'nickname'=>$usuario->getNickname(),
                'email'=>$usuario->getEmail(),
                'fotoPerfil'=>$usuario->getFotoPerfil(),
                'rol'=>$usuario->getRol(),
                'fecha_registro'=>$usuario->getFechaRegistro()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    // ============================
    // Login de usuarios
    // ============================
    #[Route('/api/users/login', name:'api_login', methods:['POST'])]
    public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email'=>$data['email']]);

        if (!$usuario) {
            return $this->json(['error'=>'Usuario no encontrado'], 404);
        }

        if (!$passwordHasher->isPasswordValid($usuario, $data['password'])) {
            return $this->json(['error'=>'Contraseña incorrecta'], 401);
        }

        // ✅ Generar JWT token
        $token = $jwtManager->create($usuario);


        // ✅ Guardarlo en la sesión
        $request->getSession()->set('token', $token);
        $request->getSession()->set('usuario_id', $usuario->getId());

        return $this->json([
            'message'=>'Login correcto',
            'usuario'=>[
                'id'=>$usuario->getId(),
                'nombre'=>$usuario->getNombre(),
                'apellido1'=>$usuario->getApellido1(),
                'apellido2'=>$usuario->getApellido2(),
                'nickname'=>$usuario->getNickname(),
                'email'=>$usuario->getEmail(),
                'telefono' => $usuario->getTelefono(),
                'direccion' => $usuario->getDireccion(),
                'fotoPerfil'=>$usuario->getFotoPerfil(),
                'rol'=>$usuario->getRol()
            ],
            'token' => $token
        ]);
    }

    // =========================
    // Listar todos los usuarios
    // =========================
    #[Route('/api/usuarios', name: 'api_usuarios_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $usuarios = $em->getRepository(Usuario::class)->findAll();
        $data = [];

        foreach ($usuarios as $u) {
            $data[] = [
                'id' => $u->getId(),
                'nombre' => $u->getNombre(),
                'apellido1' => $u->getApellido1(),
                'apellido2' => $u->getApellido2(),
                'nickname' => $u->getNickname(),
                'email' => $u->getEmail(),
                'telefono' => $u->getTelefono(),
                'direccion' => $u->getDireccion(),
                'fotoPerfil' => $u->getFotoPerfil(),
                'rol' => $u->getRol(),
                'fecha_registro' => $u->getFechaRegistro()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    // =========================
    // Ver un usuario por ID
    // =========================
    #[Route('/api/usuarios/{id}', name: 'api_usuarios_detail', methods: ['GET'])]
    public function detail(EntityManagerInterface $em, int $id): JsonResponse
    {
        $usuario = $em->getRepository(Usuario::class)->find($id);
        if (!$usuario) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        return $this->json([
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'apellido1' => $usuario->getApellido1(),
            'apellido2' => $usuario->getApellido2(),
            'nickname' => $usuario->getNickname(),
            'email' => $usuario->getEmail(),
            'telefono' => $usuario->getTelefono(),
            'direccion' => $usuario->getDireccion(),
            'fotoPerfil' => $usuario->getFotoPerfil(),
            'rol' => $usuario->getRol(),
            'fecha_registro' => $usuario->getFechaRegistro()->format('Y-m-d H:i:s'),
        ]);
    }

    // =========================
    // Crear usuario (admin)
    // =========================
    #[Route('/api/usuarios', name: 'api_usuarios_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nombre'], $data['email'], $data['contrasena'])) {
            return $this->json(['error' => 'Faltan datos obligatorios'], 400);
        }

        $usuario = new Usuario();
        $usuario->setNombre($data['nombre']);
        $usuario->setApellido1($data['apellido1'] ?? null);
        $usuario->setApellido2($data['apellido2'] ?? null);
        $usuario->setNickname($data['nickname'] ?? null);
        $usuario->setEmail($data['email']);
        $usuario->setContrasenaHash(password_hash($data['contrasena'], PASSWORD_BCRYPT));
        $usuario->setTelefono($data['telefono'] ?? null);
        $usuario->setDireccion($data['direccion'] ?? null);
        $usuario->setFotoPerfil($data['fotoPerfil'] ?? null);
        $usuario->setRol($data['rol'] ?? 'cliente');
        $usuario->setFechaRegistro(new \DateTime());

        $em->persist($usuario);
        $em->flush();

        return $this->json(['message' => 'Usuario creado', 'id' => $usuario->getId()]);
    }

    // =========================
    // Actualizar usuario
    // =========================
    #[Route('/api/usuarios/{id}', name:'api_usuarios_modificar', methods:['PUT'])]
    public function modificarUsuario(
        EntityManagerInterface $em,
        Request $request,
        int $id,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $usuario = $em->getRepository(Usuario::class)->find($id);
        if (!$usuario) {
            return $this->json(['error'=>'Usuario no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nombre'])) $usuario->setNombre($data['nombre']);
        if (isset($data['apellido1'])) $usuario->setApellido1($data['apellido1']);
        if (isset($data['apellido2'])) $usuario->setApellido2($data['apellido2']);
        if (isset($data['nickname'])) $usuario->setNickname($data['nickname']);
        if (isset($data['email'])) $usuario->setEmail($data['email']);
        if (isset($data['telefono'])) $usuario->setTelefono($data['telefono']);
        if (isset($data['direccion'])) $usuario->setDireccion($data['direccion']);
        if (isset($data['fotoPerfil'])) $usuario->setFotoPerfil($data['fotoPerfil']);
        if (isset($data['rol'])) $usuario->setRol($data['rol']);

        if (!empty($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($usuario, $data['password']);
            $usuario->setContrasenaHash($hashedPassword);
        }

        $em->flush();

        return $this->json([
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'apellido1' => $usuario->getApellido1(),
            'apellido2' => $usuario->getApellido2(),
            'nickname' => $usuario->getNickname(),
            'email' => $usuario->getEmail(),
            'telefono' => $usuario->getTelefono(),
            'direccion' => $usuario->getDireccion(),
            'fotoPerfil' => $usuario->getFotoPerfil(),
            'rol' => $usuario->getRol(),
            'fecha_registro' => $usuario->getFechaRegistro()->format('Y-m-d H:i:s'),
        ]);
    }

    // =========================
    // Eliminar usuario
    // =========================
    #[Route('/api/usuarios/{id}', name: 'api_usuarios_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $usuario = $em->getRepository(Usuario::class)->find($id);
        if (!$usuario) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        $em->remove($usuario);
        $em->flush();

        return $this->json(['message' => 'Usuario eliminado']);
    }

    // ============================
    // Recuperar contraseña
    // ============================
    #[Route('/api/users/recover-password', name: 'api_users_recover_password', methods: ['POST'])]
    public function recoverPassword(Request $request, EntityManagerInterface $em, MailerInterface $mailer): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['email'] ?? null;

            if (!$email) {
                return $this->json(['error' => 'Falta el email'], 400);
            }

            $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);
            if (!$usuario) {
                return $this->json(['error' => 'No existe usuario con ese email'], 404);
            }

            // Generar token
            $token = bin2hex(random_bytes(32));
            $usuario->setResetToken($token);
            $em->flush();

            // Enviar correo
            $resetUrl = "http://localhost:5173/reset-password?token=$token"; // Ajusta al dominio de tu frontend

            $emailMessage = (new Email())
                ->from('no-reply@senorturron.com')
                ->to($usuario->getEmail())
                ->subject('Recuperar contraseña')
                ->text("Haz clic en el siguiente enlace para restablecer tu contraseña: $resetUrl");

            $mailer->send($emailMessage);

            return $this->json(['message' => 'Correo de recuperación enviado']);

        } catch (\Throwable $e) {
            // 👇 Esto evita el “Unexpected token '<'” devolviendo un JSON aunque haya error
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/api/users/reset-password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        $newPassword = $data['new_password'] ?? null;

        if (!$token || !$newPassword) {
            return $this->json(['error' => 'Faltan datos'], 400);
        }

        $usuario = $em->getRepository(Usuario::class)->findOneBy(['resetToken' => $token]);
        if (!$usuario) {
            return $this->json(['error' => 'Token inválido o expirado'], 400);
        }

        // Cambiar la contraseña
        $hashedPassword = $passwordHasher->hashPassword($usuario, $newPassword);
        $usuario->setContrasenaHash($hashedPassword);
        $usuario->setResetToken(null); // invalidar el token

        $em->flush();

        return $this->json(['message' => 'Contraseña restablecida correctamente']);
    }
}
