<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pacientes</title>
    <link rel="stylesheet" href="../CSS/stylepaciente.css">
</head>
<body>
    <div class="container">
        <h1>Lista de Pacientes</h1>
        <button id="agregarPacienteBtn">Agregar Paciente</button>
        <button id="editarPacienteBtn">Editar Paciente</button>
        <button id="eliminarPacienteBtn">Eliminar Paciente</button>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Seleccionar</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>DNI</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody id="tabla-pacientes">
                    <!-- Aquí se cargará la lista de pacientes vía AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Agregar Paciente -->
    <div id="modalAgregar" class="modal">
        <div class="modal-content">
            <h2>Agregar Paciente</h2>
            <form id="formAgregarPaciente">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="El nombre solo debe contener letras y espacios.">
                
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="El apellido solo debe contener letras y espacios.">
                
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required pattern="\d{8}" title="El DNI debe contener 8 dígitos numéricos." maxlength="8">
                
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required pattern="\d{9}" title="El teléfono debe contener 9 dígitos numéricos." maxlength="9">
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required title="Ingrese un correo electrónico válido.">
                
                <button type="submit">Guardar</button>
                <button type="button" id="cancelarAgregar">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Paciente -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <h2>Editar Paciente</h2>
            <form id="formEditarPaciente">
                <input type="hidden" id="editarId" name="id">
                
                <label for="nombreEditar">Nombre:</label>
                <input type="text" id="nombreEditar" name="nombre" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="El nombre solo debe contener letras y espacios.">
                
                <label for="apellidoEditar">Apellido:</label>
                <input type="text" id="apellidoEditar" name="apellido" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="El apellido solo debe contener letras y espacios.">
                
                <label for="dniEditar">DNI:</label>
                <input type="text" id="dniEditar" name="dni" required pattern="\d{8}" title="El DNI debe contener 8 dígitos numéricos." maxlength="8">
                
                <label for="telefonoEditar">Teléfono:</label>
                <input type="text" id="telefonoEditar" name="telefono" required pattern="\d{9}" title="El teléfono debe contener 9 dígitos numéricos." maxlength="9">
                
                <label for="emailEditar">Email:</label>
                <input type="email" id="emailEditar" name="email" required title="Ingrese un correo electrónico válido.">
                
                <button type="submit">Guardar Cambios</button>
                <button type="button" id="cancelarEditar">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="../JS/pacientes.js"></script>
</body>
</html>
