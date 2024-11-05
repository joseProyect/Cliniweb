<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../CSS/styleUsua.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Usuarios</h1>
        <button id="agregar-usuario-btn">
            <i class="fas fa-user-plus"></i> Agregar Usuario
        </button>
        <button id="editar-usuario-btn">
            <i class="fas fa-pencil-alt"></i> Editar Usuario
        </button>
        <button id="eliminar-usuario-btn">
            <i class="fas fa-trash-alt"></i> Eliminar Usuario
        </button>
        
        <!-- Tabla de Usuarios -->
        <table>
            <thead>
                <tr>
                    <th>Seleccionar</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Contraseña</th>
                    <th>Estado</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include_once 'UsuarioDAO.php';
                $usuarioDAO = new UsuarioDAO();
                $usuarios = $usuarioDAO->obtenerUsuarios();

                if ($usuarios) {
                    foreach ($usuarios as $usuario) {
                        echo "<tr>
                                <td><input type='checkbox' class='select-user' data-id='" . htmlspecialchars($usuario['id']) . "'></td>
                                <td data-label='Nombre'>" . htmlspecialchars($usuario['nombre']) . "</td>
                                <td data-label='Email'>" . htmlspecialchars($usuario['email']) . "</td>
                                <td data-label='Contraseña'>" . htmlspecialchars($usuario['contraseña']) . "</td>
                                <td data-label='Estado'>" . ($usuario['estado'] ? 'Activo' : 'Inactivo') . "</td>
                                <td data-label='Rol'>" . htmlspecialchars($usuario['nombre_rol']) . "</td>
                                <td data-label='Acciones'>
                                    <a href='#' class='edit' data-id='" . htmlspecialchars($usuario['id']) . "'>
                                        <i class='fas fa-pencil-alt'></i> Editar
                                    </a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay usuarios registrados</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Formulario para agregar usuario -->
        <div id="formulario-agregar" class="modal" style="display: none;">
            <div class="modal-content">
                <h2>Agregar Usuario</h2>
                <form id="form-agregar-usuario">
                    <div class="formulario-fila">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="formulario-fila">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="formulario-fila">
                        <label for="contrasena">Contraseña:</label>
                        <input type="password" id="contrasena" name="contrasena" required>
                    </div>

                    <div class="formulario-fila">
                        <label for="rol">Rol:</label>
                        <select id="rol" name="rol" required>
                            <option value="" disabled selected>Seleccione un rol</option>
                            <option value="2">Administrador</option>
                            <option value="3">Doctor</option>
                            <option value="4">Asistente</option>
                        </select>
                    </div>

                    <div class="form-buttons">
                        <button type="button" id="cancelar-btn" class="btn btn-danger">Cancelar</button>
                        <button type="submit" id="guardar-btn" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Formulario para editar usuario -->
        <div id="formulario-editar" class="modal" style="display: none;">
            <div class="modal-content">
                <h2>Editar Usuario</h2>
                <form id="form-editar-usuario">
                    <input type="hidden" id="usuario-id" name="usuario-id">
                    <div class="formulario-fila">
                        <label for="nombre-editar">Nombre:</label>
                        <input type="text" id="nombre-editar" name="nombre" required>
                    </div>

                    <div class="formulario-fila">
                        <label for="email-editar">Email:</label>
                        <input type="email" id="email-editar" name="email" required>
                    </div>

                    <div class="formulario-fila">
                        <label for="contrasena-editar">Contraseña:</label>
                        <input type="password" id="contrasena-editar" name="contraseña" required>
                    </div>

                    <div class="formulario-fila">
                        <label for="estado-editar">Estado:</label>
                        <select id="estado-editar" name="estado" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <div class="formulario-fila">
                        <label for="rol-editar">Rol:</label>
                        <select id="rol-editar" name="rol" required>
                            <option value="" disabled selected>Seleccione un rol</option>
                            <option value="2">Administrador</option>
                            <option value="3">Doctor</option>
                            <option value="4">Asistente</option>
                        </select>
                    </div>

                    <div class="form-buttons">
                        <button type="button" id="cancelar-edicion-btn" class="btn btn-danger">Cancelar</button>
                        <button type="submit" id="guardar-cambios-btn" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../JS/Usua.js"></script>
</body>
</html>
