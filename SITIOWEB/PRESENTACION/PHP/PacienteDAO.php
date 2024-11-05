<?php
// Ruta: PRESENTACIÓN/PHP/PacienteDAO.php

include_once '../../DATOS/Database.php';

class PacienteDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los pacientes
    public function obtenerPacientes() {
        $query = "SELECT * FROM Pacientes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un paciente por su ID
    public function obtenerPacientePorId($id) {
        $query = "SELECT * FROM Pacientes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Agregar un nuevo paciente
    public function agregarPaciente($nombre, $apellido, $dni, $telefono, $email) {
        $query = "INSERT INTO Pacientes (nombre, apellido, dni, telefono, email) VALUES (:nombre, :apellido, :dni, :telefono, :email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // Editar un paciente existente
    public function editarPaciente($id, $nombre, $apellido, $dni, $telefono, $email) {
        $query = "UPDATE Pacientes SET nombre = :nombre, apellido = :apellido, dni = :dni, telefono = :telefono, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // Eliminar un paciente
    public function eliminarPaciente($id) {
        $query = "DELETE FROM Pacientes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

// Manejo de solicitudes AJAX
header('Content-Type: application/json');
$pacienteDAO = new PacienteDAO();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) {
    if ($_GET['accion'] === 'listar') {
        echo json_encode($pacienteDAO->obtenerPacientes());
    } elseif ($_GET['accion'] === 'obtener' && isset($_GET['id'])) {
        $paciente = $pacienteDAO->obtenerPacientePorId($_GET['id']);
        if ($paciente) {
            echo json_encode($paciente);
        } else {
            echo json_encode(["success" => false, "message" => "Paciente no encontrado"]);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    $response = ["success" => false];

    if ($accion === 'agregar') {
        $response['success'] = $pacienteDAO->agregarPaciente($_POST['nombre'], $_POST['apellido'], $_POST['dni'], $_POST['telefono'], $_POST['email']);
        $response['message'] = $response['success'] ? "Paciente agregado correctamente" : "Error al agregar paciente";
    } elseif ($accion === 'editar') {
        $response['success'] = $pacienteDAO->editarPaciente($_POST['id'], $_POST['nombre'], $_POST['apellido'], $_POST['dni'], $_POST['telefono'], $_POST['email']);
        $response['message'] = $response['success'] ? "Paciente editado correctamente" : "Error al editar paciente";
    } elseif ($accion === 'eliminar' && isset($_POST['id'])) {
        $response['success'] = $pacienteDAO->eliminarPaciente($_POST['id']);
        $response['message'] = $response['success'] ? "Paciente eliminado correctamente" : "Error al eliminar paciente";
    }

    echo json_encode($response);
}
?>
