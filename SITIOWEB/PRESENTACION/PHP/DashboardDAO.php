<?php
require_once '../../DATOS/Database.php';

class DashboardDAO {

    // Obtener el número de usuarios activos
    public function obtenerNumeroUsuarios() {
        $db = new Database();
        $conn = $db->getConnection();
        $sql = "SELECT COUNT(*) AS total_usuarios FROM Usuarios WHERE estado = 1";
        $stmt = $conn->query($sql);
        $fila = $stmt->fetch();
        return $fila['total_usuarios'];
    }

    // Obtener el número de pacientes registrados
    public function obtenerNumeroPacientes() {
        $db = new Database();
        $conn = $db->getConnection();
        $sql = "SELECT COUNT(*) AS total_pacientes FROM Pacientes";
        $stmt = $conn->query($sql);
        $fila = $stmt->fetch();
        return $fila['total_pacientes'];
    }

    // Obtener próximas citas hasta el próximo viernes
    public function obtenerProximasCitas() {
        $db = new Database();
        $conn = $db->getConnection();
        $sql = "SELECT Pacientes.nombre, Citas.fecha, Citas.hora, Citas.estado 
                FROM Citas 
                INNER JOIN Pacientes ON Citas.paciente_id = Pacientes.id 
                WHERE Citas.fecha <= DATE_ADD(CURDATE(), INTERVAL (5 - WEEKDAY(CURDATE())) DAY) 
                ORDER BY Citas.fecha, Citas.hora";
        $stmt = $conn->query($sql);
        $citas = [];
        while ($fila = $stmt->fetch()) {
            $citas[] = $fila;
        }
        return $citas;
    }
}
?>
