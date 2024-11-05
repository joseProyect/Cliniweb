<?php
// Iniciar la sesión y verificar autenticación si es necesario (opcional)
// session_start();
// if (!isset($_SESSION['usuario'])) {
//     header('Location: login.php');
//     exit;
// }

// Incluir la conexión a la base de datos
include('../../DATOS/Database.php');
$db = new Database();
$conn = $db->getConnection();

// Consulta para obtener todas las citas junto con la información del paciente y del doctor
$query = "SELECT Citas.id, Pacientes.nombre AS paciente_nombre, Pacientes.apellido AS paciente_apellido, 
          Usuarios.nombre AS doctor_nombre, Citas.fecha, Citas.hora, Citas.estado
          FROM Citas
          JOIN Pacientes ON Citas.paciente_id = Pacientes.id
          JOIN Usuarios ON Citas.doctor_id = Usuarios.id";

$stmt = $conn->prepare($query);
$stmt->execute();
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas</title>
    <link rel="stylesheet" href="../CSS/citas.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Citas</h1>
        <div class="button-container">
            <button id="agregar-cita-btn">Agregar Cita</button>
            <button id="editar-cita-btn">Editar Cita</button>
        </div>

        <table id="tabla-citas">
            <thead>
                <tr>
                    <th>SELECCIONAR</th>
                    <th>PACIENTE</th>
                    <th>DOCTOR</th>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($citas as $cita): ?>
                    <tr data-id="<?php echo $cita['id']; ?>">
                        <td><input type="radio" name="seleccionar_cita" class="seleccionar-cita" value="<?php echo $cita['id']; ?>"></td>
                        <td><?php echo htmlspecialchars($cita['paciente_nombre'] . ' ' . $cita['paciente_apellido']); ?></td>
                        <td><?php echo htmlspecialchars($cita['doctor_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($cita['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($cita['hora']); ?></td>
                        <td><?php echo htmlspecialchars($cita['estado']); ?></td>
                        <td class="actions">
                            <a href="#" class="delete" data-id="<?php echo $cita['id']; ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Formulario Modal para Agregar/Editar Cita -->
    <div id="formulario-cita" class="modal">
        <div class="modal-content">
            <h2 id="modal-title">Agregar Cita</h2>
            <form id="form-cita">
                <input type="hidden" id="cita-id" name="cita_id">
                
                <label for="paciente">DNI del Paciente:</label>
                <input type="text" id="paciente" name="paciente" required>
                <button type="button" id="buscar-paciente-btn">Buscar Paciente</button>
                <p id="paciente-resultado">Paciente no seleccionado</p>
                
                <label for="doctor">Doctor:</label>
                <select id="doctor" name="doctor" required>
                    <!-- Opciones de doctores aquí (rellenar dinámicamente o estáticamente) -->
                </select>

                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>

                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" required>

                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="pendiente">Pendiente</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                </select>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"></textarea>

                <div class="form-buttons">
                    <button type="button" id="cancelar-btn">Cancelar</button>
                    <button type="submit" id="guardar-btn">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../JS/citas.js"></script>
</body>
</html>
