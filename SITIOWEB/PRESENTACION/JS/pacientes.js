document.addEventListener('DOMContentLoaded', function () {
    const agregarPacienteBtn = document.getElementById('agregarPacienteBtn');
    const editarPacienteBtn = document.getElementById('editarPacienteBtn');
    const eliminarPacienteBtn = document.getElementById('eliminarPacienteBtn');

    const modalAgregar = document.getElementById('modalAgregar');
    const modalEditar = document.getElementById('modalEditar');

    const formAgregarPaciente = document.getElementById('formAgregarPaciente');
    const formEditarPaciente = document.getElementById('formEditarPaciente');

    // Mostrar formulario de agregar paciente en el modal
    agregarPacienteBtn.addEventListener('click', () => {
        modalAgregar.style.display = 'block';
    });

    // Cerrar el modal de agregar paciente
    document.getElementById('cancelarAgregar').addEventListener('click', () => {
        modalAgregar.style.display = 'none';
        formAgregarPaciente.reset();
    });

    // Mostrar formulario de editar paciente en el modal
    editarPacienteBtn.addEventListener('click', () => {
        const pacienteId = document.querySelector('input[name="seleccionarPaciente"]:checked');
        if (pacienteId) {
            fetch(`../PHP/PacienteDAO.php?accion=obtener&id=${pacienteId.value}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editarId').value = data.id;
                    document.getElementById('nombreEditar').value = data.nombre;
                    document.getElementById('apellidoEditar').value = data.apellido;
                    document.getElementById('dniEditar').value = data.dni;
                    document.getElementById('telefonoEditar').value = data.telefono;
                    document.getElementById('emailEditar').value = data.email;
                    modalEditar.style.display = 'block';
                })
                .catch(error => alert('Error al cargar los datos del paciente: ' + error));
        } else {
            alert("Seleccione un paciente para editar.");
        }
    });

    // Cerrar el modal de editar paciente
    document.getElementById('cancelarEditar').addEventListener('click', () => {
        modalEditar.style.display = 'none';
        formEditarPaciente.reset();
    });

    // Agregar paciente
    formAgregarPaciente.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(formAgregarPaciente);
        fetch('../PHP/PacienteDAO.php', {
            method: 'POST',
            body: new URLSearchParams([...formData, ['accion', 'agregar']])
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Paciente agregado');
                modalAgregar.style.display = 'none';
                formAgregarPaciente.reset();
                cargarPacientes();
            } else {
                alert('Error al agregar paciente');
            }
        })
        .catch(error => alert('Error al agregar paciente: ' + error));
    });

    // Editar paciente
    formEditarPaciente.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(formEditarPaciente);
        fetch('../PHP/PacienteDAO.php', {
            method: 'POST',
            body: new URLSearchParams([...formData, ['accion', 'editar']])
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Paciente editado');
                modalEditar.style.display = 'none';
                formEditarPaciente.reset();
                cargarPacientes();
            } else {
                alert('Error al editar paciente');
            }
        })
        .catch(error => alert('Error al editar paciente: ' + error));
    });

    // Función para cargar la lista de pacientes
    function cargarPacientes() {
        fetch('../PHP/PacienteDAO.php?accion=listar')
            .then(response => response.json())
            .then(data => {
                const tablaPacientes = document.getElementById('tabla-pacientes');
                tablaPacientes.innerHTML = ''; // Limpiar la tabla antes de cargar los datos

                data.forEach(paciente => {
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td><input type="radio" name="seleccionarPaciente" value="${paciente.id}"></td>
                        <td>${paciente.nombre}</td>
                        <td>${paciente.apellido}</td>
                        <td>${paciente.dni}</td>
                        <td>${paciente.telefono}</td>
                        <td>${paciente.email}</td>
                        <td>${paciente.fecha_registro}</td>
                    `;
                    tablaPacientes.appendChild(fila);
                });
            })
            .catch(error => console.error('Error al cargar pacientes:', error));
    }

    // Función para eliminar paciente
    eliminarPacienteBtn.addEventListener('click', () => {
        const pacienteId = document.querySelector('input[name="seleccionarPaciente"]:checked');
        if (pacienteId) {
            if (confirm("¿Está seguro de que desea eliminar este paciente?")) {
                fetch('../PHP/PacienteDAO.php', {
                    method: 'POST',
                    body: new URLSearchParams({ 'accion': 'eliminar', 'id': pacienteId.value })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Paciente eliminado');
                        cargarPacientes();
                    } else {
                        alert('Error al eliminar paciente');
                    }
                })
                .catch(error => alert('Error al eliminar paciente: ' + error));
            }
        } else {
            alert("Seleccione un paciente para eliminar.");
        }
    });

    // Llama a la función para cargar los pacientes al cargar la página
    cargarPacientes();
});

