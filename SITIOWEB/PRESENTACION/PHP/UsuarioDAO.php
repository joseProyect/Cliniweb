<?php
// Archivo: /PRESENTACION/Administrador/TABLASPHP/UsuarioDAO.php

include_once '../../DATOS/Database.php';

class UsuarioDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los usuarios junto con su rol y contraseña
    public function obtenerUsuarios() {
        $query = "
            SELECT 
                u.id, 
                u.nombre, 
                u.email, 
                u.contraseña, 
                u.estado, 
                r.nombre AS nombre_rol
            FROM 
                Usuarios u
            LEFT JOIN 
                Roles r ON u.rol_id = r.id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Agregar un nuevo usuario SIN encriptación de la contraseña
    public function agregarUsuario($nombre, $email, $contrasena, $rol_id) {
        try {
            $query = "
                INSERT INTO Usuarios (nombre, email, contraseña, rol_id) 
                VALUES (:nombre, :email, :contrasena, :rol_id)
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contrasena', $contrasena);
            $stmt->bindParam(':rol_id', $rol_id);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Error al ejecutar la consulta SQL.");
            }
        } catch (Exception $e) {
            // Devolver el error en formato JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => "Error al agregar usuario: " . $e->getMessage()]);
            exit;
        }
    }

    // Eliminar un usuario por ID
    public function eliminarUsuario($id) {
        try {
            $query = "DELETE FROM Usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => "Error al eliminar usuario: " . $e->getMessage()]);
            exit;
        }
    }

    // Obtener un usuario por ID (incluye la contraseña)
    public function obtenerUsuarioPorId($id) {
        $query = "
            SELECT 
                u.id, 
                u.nombre, 
                u.email, 
                u.contraseña,  
                u.estado, 
                r.nombre AS nombre_rol, 
                u.rol_id
            FROM 
                Usuarios u
            LEFT JOIN 
                Roles r ON u.rol_id = r.id
            WHERE 
                u.id = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Editar un usuario por ID, SIN encriptación de contraseña
    public function editarUsuario($id, $nombre, $email, $contrasena = null, $estado, $rol_id) {
        try {
            if ($contrasena !== null && $contrasena !== '') {
                $query = "
                    UPDATE Usuarios 
                    SET nombre = :nombre, email = :email, contraseña = :contrasena, estado = :estado, rol_id = :rol_id 
                    WHERE id = :id
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':contrasena', $contrasena);
            } else {
                $query = "
                    UPDATE Usuarios 
                    SET nombre = :nombre, email = :email, estado = :estado, rol_id = :rol_id 
                    WHERE id = :id
                ";
                $stmt = $this->conn->prepare($query);
            }

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':rol_id', $rol_id);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Error al ejecutar la consulta de actualización.");
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => "Error al editar usuario: " . $e->getMessage()]);
            exit;
        }
    }
}

// Manejar solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioDAO = new UsuarioDAO();
    if (isset($_POST['usuario-id']) && $_POST['usuario-id'] !== '') {
        // Editar un usuario
        $id = $_POST['usuario-id'];
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $contrasena = $_POST['contraseña'] ?? '';
        $estado = $_POST['estado'] ?? '1';
        $rol_id = $_POST['rol'] ?? 2;

        $resultado = $usuarioDAO->editarUsuario($id, $nombre, $email, $contrasena, $estado, $rol_id);

        header('Content-Type: application/json');
        echo json_encode(['success' => $resultado]);
        exit;
    } else {
        // Agregar un usuario nuevo
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';
        $rol_id = $_POST['rol'] ?? 2;

        $resultado = $usuarioDAO->agregarUsuario($nombre, $email, $contrasena, $rol_id);

        header('Content-Type: application/json');
        echo json_encode(['success' => $resultado]);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Eliminar un usuario
    if (isset($_GET['id'])) {
        $usuarioDAO = new UsuarioDAO();
        $id = $_GET['id'];
        $resultado = $usuarioDAO->eliminarUsuario($id);

        header('Content-Type: application/json');
        echo json_encode(['success' => $resultado]);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Obtener datos de un usuario específico
    $usuarioDAO = new UsuarioDAO();
    $id = $_GET['id'];
    $usuario = $usuarioDAO->obtenerUsuarioPorId($id);

    header('Content-Type: application/json');
    echo json_encode($usuario);
    exit;
}
?>
