document.addEventListener('DOMContentLoaded', function () {
    const agregarUsuarioBtn = document.getElementById('agregar-usuario-btn');
    const editarUsuarioBtn = document.getElementById('editar-usuario-btn');
    const eliminarUsuarioBtn = document.getElementById('eliminar-usuario-btn');
    const formularioAgregar = document.getElementById('formulario-agregar');
    const formularioEditar = document.getElementById('formulario-editar');
    const formAgregar = document.getElementById('form-agregar-usuario');
    const formEditar = document.getElementById('form-editar-usuario');

    // Función para limpiar los formularios
    function limpiarFormulario(form) {
        form.reset();
    }

    // Mostrar el formulario de agregar usuario
    agregarUsuarioBtn.addEventListener('click', function () {
        limpiarFormulario(formAgregar);
        formularioAgregar.style.display = 'block';
    });

    // Ocultar el formulario de agregar usuario al cancelar
    document.getElementById('cancelar-btn').addEventListener('click', function () {
        formularioAgregar.style.display = 'none';
    });

    // Ocultar el formulario de editar usuario al cancelar
    document.getElementById('cancelar-edicion-btn').addEventListener('click', function () {
        formularioEditar.style.display = 'none';
    });

    // Enviar formulario de agregar usuario
    formAgregar.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(formAgregar);

        fetch('../PHP/UsuarioDAO.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Usuario agregado correctamente');
                formularioAgregar.style.display = 'none';
                location.reload();
            } else {
                alert('Error al agregar el usuario: ' + data.message);
            }
        })
        .catch(error => console.error('Error en la solicitud:', error));
    });

    // Función para cargar datos en el formulario de edición y mostrarlo
    function cargarFormularioEditar(userId) {
        fetch(`../PHP/UsuarioDAO.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('usuario-id').value = data.id;
                    document.getElementById('nombre-editar').value = data.nombre;
                    document.getElementById('email-editar').value = data.email;
                    document.getElementById('contrasena-editar').value = data.contraseña;
                    document.getElementById('estado-editar').value = data.estado;
                    document.getElementById('rol-editar').value = data.rol_id;
                    formularioEditar.style.display = 'block';
                }
            })
            .catch(error => console.error('Error al cargar los datos:', error));
    }

    // Mostrar el formulario de edición al hacer clic en el botón "Editar Usuario"
    editarUsuarioBtn.addEventListener('click', function () {
        const selectedUsers = document.querySelectorAll('.select-user:checked');
        if (selectedUsers.length === 1) {
            const userId = selectedUsers[0].dataset.id;
            cargarFormularioEditar(userId);
        } else {
            alert('Selecciona solo un usuario para editar.');
        }
    });

    // Añadir eventos a los enlaces "Editar" en la tabla
    document.querySelectorAll('.edit').forEach(editLink => {
        editLink.addEventListener('click', function (event) {
            event.preventDefault(); // Evita el comportamiento predeterminado del enlace
            const userId = this.getAttribute('data-id');
            cargarFormularioEditar(userId);
        });
    });

    // Enviar formulario de editar usuario
    formEditar.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(formEditar);
        formData.append('usuario-id', document.getElementById('usuario-id').value);

        fetch('../PHP/UsuarioDAO.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Usuario editado correctamente');
                formularioEditar.style.display = 'none';
                location.reload();
            } else {
                alert('Error al editar el usuario: ' + data.message);
            }
        })
        .catch(error => console.error('Error en la solicitud:', error));
    });

    // Eliminar usuarios seleccionados
    eliminarUsuarioBtn.addEventListener('click', function () {
        const selectedUsers = document.querySelectorAll('.select-user:checked');
        if (selectedUsers.length > 0) {
            if (confirm('¿Deseas eliminar los usuarios seleccionados?')) {
                selectedUsers.forEach(user => {
                    const userId = user.dataset.id;
                    fetch(`../PHP/UsuarioDAO.php?id=${userId}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Usuario ${userId} eliminado`);
                            location.reload();
                        } else {
                            alert('Error al eliminar el usuario.');
                        }
                    })
                    .catch(error => console.error('Error al eliminar usuario:', error));
                });
            }
        } else {
            alert('Selecciona al menos un usuario para eliminar.');
        }
    });
});
