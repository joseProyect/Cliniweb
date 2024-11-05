<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../../DATOS/Database.php');

$db = new Database();
$conn = $db->getConnection();

header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Error en la conexión a la base de datos.']);
    exit;
}

// Función para verificar si un registro existe en una tabla específica
function checkRecordExists($conn, $table, $id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Código para agregar o actualizar citas
        $paciente_id = $_POST['paciente'];
        $doctor_id = $_POST['doctor'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $estado = $_POST['estado'];
        $descripcion = $_POST['descripcion'] ?? null;

        if (isset($_POST['cita_id']) && !empty($_POST['cita_id'])) {
            // Actualizar una cita existente
            $cita_id = $_POST['cita_id'];
            $stmt = $conn->prepare("UPDATE Citas SET paciente_id = :paciente, doctor_id = :doctor, fecha = :fecha, hora = :hora, estado = :estado, descripcion = :descripcion WHERE id = :id");
            $stmt->bindParam(':paciente', $paciente_id);
            $stmt->bindParam(':doctor', $doctor_id);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora', $hora);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id', $cita_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Cita actualizada correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la cita.']);
            }
        } else {
            // Agregar una nueva cita
            $stmt = $conn->prepare("INSERT INTO Citas (paciente_id, doctor_id, fecha, hora, estado, descripcion) VALUES (:paciente, :doctor, :fecha, :hora, :estado, :descripcion)");
            $stmt->bindParam(':paciente', $paciente_id);
            $stmt->bindParam(':doctor', $doctor_id);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora', $hora);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':descripcion', $descripcion);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Cita agregada correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al agregar la cita.']);
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['dni'])) {
            // Buscar paciente por DNI
            $dni = $_GET['dni'];
            $stmt = $conn->prepare("SELECT id, nombre, apellido FROM Pacientes WHERE dni = :dni");
            $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
            $stmt->execute();
            $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($paciente) {
                echo json_encode(['success' => true, 'nombre' => $paciente['nombre'], 'apellido' => $paciente['apellido'], 'id' => $paciente['id']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Paciente no encontrado']);
            }
        } elseif (isset($_GET['doctores'])) {
            // Obtener todos los doctores
            $stmt = $conn->prepare("SELECT id, nombre FROM Usuarios WHERE rol_id = 3 AND estado = 1");
            $stmt->execute();
            $doctores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($doctores);
        } elseif (isset($_GET['id'])) {
            // Obtener una cita específica para editar
            $cita_id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM Citas WHERE id = :id");
            $stmt->bindParam(':id', $cita_id, PDO::PARAM_INT);
            $stmt->execute();
            $cita = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cita) {
                echo json_encode($cita);
            } else {
                echo json_encode(['success' => false, 'message' => 'La cita no existe.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Eliminar una cita
        parse_str(file_get_contents("php://input"), $deleteData);
        $cita_id = $deleteData['id'] ?? null;

        if ($cita_id && checkRecordExists($conn, 'Citas', $cita_id)) {
            $stmt = $conn->prepare("DELETE FROM Citas WHERE id = :id");
            $stmt->bindParam(':id', $cita_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la cita.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El ID de la cita no existe.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}

?>
