<?php
require_once('../../DATOS/Database.php');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'usuarios':
        getUsuarios();
        break;
    case 'pacientes':
        getPacientes();
        break;
    case 'citas':
        getProximasCitas();
        break;
    case 'buscarPaciente':
        buscarPaciente();
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
}

// Función para obtener el total de usuarios activos
function getUsuarios() {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "SELECT COUNT(*) as total_usuarios FROM Usuarios WHERE estado = 1";
    $stmt = $conn->query($sql);
    $data = $stmt->fetch();
    echo json_encode(['total_usuarios' => $data['total_usuarios']]);
}

// Función para obtener el total de pacientes
function getPacientes() {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "SELECT COUNT(*) as total_pacientes FROM Pacientes";
    $stmt = $conn->query($sql);
    $data = $stmt->fetch();
    echo json_encode(['total_pacientes' => $data['total_pacientes']]);
}

// Función para obtener las próximas citas hasta el próximo viernes
function getProximasCitas() {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "SELECT Pacientes.nombre, Citas.fecha, Citas.hora, Citas.estado 
            FROM Citas 
            JOIN Pacientes ON Citas.paciente_id = Pacientes.id 
            WHERE Citas.fecha <= DATE_ADD(CURDATE(), INTERVAL (5 - WEEKDAY(CURDATE())) DAY)";
    $stmt = $conn->query($sql);
    
    $citas = [];
    while ($row = $stmt->fetch()) {
        $citas[] = [
            'nombre' => $row['nombre'],
            'fecha' => $row['fecha'],
            'hora' => $row['hora'],
            'estado' => $row['estado']
        ];
    }
    echo json_encode(['proximas_citas' => $citas]);
}

// Función para buscar un paciente por DNI y obtener su próxima cita
function buscarPaciente() {
    $db = new Database();
    $conn = $db->getConnection();
    $dni = $_GET['dni'] ?? '';

    if (!$dni) {
        echo json_encode(['error' => 'DNI no proporcionado']);
        return;
    }

    // Obtener información básica del paciente
    $sqlPaciente = "SELECT nombre, apellido FROM Pacientes WHERE dni = ?";
    $stmtPaciente = $conn->prepare($sqlPaciente);
    $stmtPaciente->execute([$dni]);
    $paciente = $stmtPaciente->fetch();

    if ($paciente) {
        // Buscar la próxima cita del paciente
        $sqlCita = "SELECT fecha, hora, estado FROM Citas WHERE paciente_id = (SELECT id FROM Pacientes WHERE dni = ?) AND fecha >= CURDATE() ORDER BY fecha, hora LIMIT 1";
        $stmtCita = $conn->prepare($sqlCita);
        $stmtCita->execute([$dni]);
        $cita = $stmtCita->fetch();

        // Preparar respuesta con la información del paciente y su próxima cita (si la tiene)
        echo json_encode([
            'paciente' => [
                'nombre' => $paciente['nombre'],
                'apellido' => $paciente['apellido'],
                'cita' => $cita ? [
                    'fecha' => $cita['fecha'],
                    'hora' => $cita['hora'],
                    'estado' => $cita['estado']
                ] : null // Si no tiene cita, asigna null
            ]
        ]);
    } else {
        echo json_encode(['paciente' => null]);
    }
}
?>
