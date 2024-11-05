<?php
include('../../DATOS/Database.php');

class CitasDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las citas
    public function obtenerCitas() {
        $query = "SELECT Citas.id, Pacientes.nombre AS paciente_nombre, Pacientes.apellido AS paciente_apellido, 
                  Usuarios.nombre AS doctor_nombre, Usuarios.id AS doctor_id, Citas.fecha, Citas.hora, Citas.estado, Citas.descripcion
                  FROM Citas
                  JOIN Pacientes ON Citas.paciente_id = Pacientes.id
                  JOIN Usuarios ON Citas.doctor_id = Usuarios.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar una cita existente
    public function actualizarCita($id, $doctor, $fecha, $hora, $estado, $descripcion) {
        $query = "UPDATE Citas SET doctor_id = :doctor, fecha = :fecha, hora = :hora, estado = :estado, descripcion = :descripcion WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':doctor', $doctor);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':descripcion', $descripcion);

        return $stmt->execute();
    }

    // Obtener todos los doctores basados en el rol
    public function obtenerDoctores() {
        $query = "SELECT id, nombre FROM Usuarios WHERE rol_id = 3";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Instancia de la clase CitasDAO
$citasDAO = new CitasDAO();

// Manejo de las peticiones
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'obtenerDoctores') {
        // Obtener todos los doctores
        $doctores = $citasDAO->obtenerDoctores();
        echo json_encode($doctores);
    } else {
        // Obtener todas las citas
        $citas = $citasDAO->obtenerCitas();
        echo json_encode($citas);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer los datos enviados en formato JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Validar y asignar los datos de la cita
    $id = $data['id'];
    $doctor = $data['doctor'];
    $fecha = $data['fecha'];
    $hora = $data['hora'];
    $estado = $data['estado'];
    $descripcion = $data['descripcion'];

    // Actualizar la cita en la base de datos
    $success = $citasDAO->actualizarCita($id, $doctor, $fecha, $hora, $estado, $descripcion);

    // Devolver el resultado en formato JSON
    echo json_encode(['success' => $success]);
}
?>
