<?php
require_once '../../DATOS/Database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = strtolower(trim($_POST['usuario']));
    $contrasena = trim($_POST['contrasena']);
    $rol = strtolower(trim($_POST['rol']));

    $db = new Database();
    $pdo = $db->getConnection();

    try {
        // Consulta que incluye el estado del usuario
        $query = "SELECT id, nombre, contraseña, rol_id, estado FROM Usuarios WHERE LOWER(email) = :usuario LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['usuario' => $usuario]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();

            // Verificar si el estado es activo (1: activo)
            if ($user['estado'] === 1) {
                // Verificar la contraseña directamente
                if ($contrasena === $user['contraseña']) {
                    // Consulta para obtener el nombre del rol
                    $queryRol = "SELECT LOWER(nombre) FROM Roles WHERE id = :rol_id LIMIT 1";
                    $stmtRol = $pdo->prepare($queryRol);
                    $stmtRol->execute(['rol_id' => $user['rol_id']]);
                    $rolUsuario = $stmtRol->fetchColumn();

                    // Validar rol
                    if ($rolUsuario === $rol) {
                        if ($rol === 'administrador') {
                            header('Location: ../../PRESENTACION/menu-administrador.html');
                        } elseif ($rol === 'doctor') {
                            header('Location: ../../PRESENTACION/menu-administrador.html');
                        } elseif ($rol === 'asistente') {
                            header('Location: ../../PRESENTACION/menu-administrador.html');
                        }
                        exit;
                    } else {
                        echo "<script>alert('Rol incorrecto seleccionado'); window.location.href = '../../Login.html';</script>";
                    }
                } else {
                    echo "<script>alert('Contraseña incorrecta'); window.location.href = '../../Login.html';</script>";
                }
            } else {
                echo "<script>alert('El usuario está inactivo'); window.location.href = '../../Login.html';</script>";
            }
        } else {
            echo "<script>alert('Usuario no encontrado'); window.location.href = '../../Login.html';</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
