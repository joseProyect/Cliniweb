document.addEventListener('DOMContentLoaded', function () {
    const agregarCitaBtn = document.getElementById('agregar-cita-btn');
    const editarCitaBtn = document.getElementById('editar-cita-btn');
    const formularioCita = document.getElementById('formulario-cita');
    const formCita = document.getElementById('form-cita');
    const cancelarBtn = document.getElementById('cancelar-btn');
    const modalTitle = document.getElementById('modal-title');
    const citaIdInput = document.getElementById('cita-id');
    const pacienteInput = document.getElementById('paciente');
    const buscarPacienteBtn = document.getElementById('buscar-paciente-btn');
    const pacienteResultado = document.getElementById('paciente-resultado');
    const doctorSelect = document.getElementById('doctor');
    const tablaCitas = document.getElementById('tabla-citas');
    let selectedCitaId = null;

    // Mostrar el formulario de agregar cita
    agregarCitaBtn.addEventListener('click', function () {
        formCita.reset();
        citaIdInput.value = '';
        modalTitle.textContent = "Agregar Cita";
        formularioCita.style.display = 'block';
        pacienteResultado.textContent = "Paciente no seleccionado";
        delete pacienteResultado.dataset.id;

        pacienteInput.disabled = false;
        buscarPacienteBtn.disabled = false;

        cargarDoctores();
    });

    // Mostrar el formulario de editar cita
    editarCitaBtn.addEventListener('click', function () {
        if (selectedCitaId) {
            cargarCita(selectedCitaId);
        } else {
            alert("Selecciona una cita de la tabla para editar.");
        }
    });

    // Ocultar el formulario de agregar/editar cita
    cancelarBtn.addEventListener('click', function () {
        formularioCita.style.display = 'none';
    });

    // Cargar doctores
    function cargarDoctores() {
        fetch('../PHP/CitaDAO.php?doctores=1')
            .then(response => response.json())
            .then(data => {
                doctorSelect.innerHTML = '';
                data.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = doctor.nombre;
                    doctorSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar los doctores:', error));
    }

    // Buscar paciente por DNI
    buscarPacienteBtn.addEventListener('click', function () {
        const dni = pacienteInput.value.trim();
        if (dni === "") {
            alert("Por favor ingrese un DNI para buscar.");
            return;
        }

        fetch(`../PHP/CitaDAO.php?dni=${dni}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    pacienteResultado.textContent = `Paciente: ${data.nombre} ${data.apellido}`;
                    pacienteResultado.dataset.id = data.id;
                } else {
                    pacienteResultado.textContent = "Paciente no encontrado";
                    delete pacienteResultado.dataset.id;
                }
            })
            .catch(error => console.error('Error al buscar el paciente:', error));
    });

    // Enviar formulario de agregar/editar cita
    formCita.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(formCita);

        const pacienteId = pacienteResultado.dataset.id;
        if (!pacienteId) {
            alert('Por favor, selecciona un paciente válido.');
            return;
        }
        formData.append('paciente', pacienteId);

        const isEdit = Boolean(citaIdInput.value);
        formData.append('cita_id', citaIdInput.value);

        fetch('../PHP/CitaDAO.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(isEdit ? 'Cita actualizada correctamente' : 'Cita agregada correctamente');
                formularioCita.style.display = 'none';
                location.reload();
            } else {
                alert('Hubo un problema al procesar la cita.');
            }
        })
        .catch(error => console.error('Error en la solicitud:', error));
    });

    // Cargar datos de la cita para editar
    function cargarCita(id) {
        fetch('../PHP/CitaDAO.php?doctores=1')
            .then(response => response.json())
            .then(doctores => {
                doctorSelect.innerHTML = '';
                doctores.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = doctor.nombre;
                    doctorSelect.appendChild(option);
                });
                return fetch(`../PHP/CitaDAO.php?id=${id}`);
            })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    citaIdInput.value = data.id;
                    pacienteResultado.textContent = `Paciente: ${data.paciente_nombre} ${data.paciente_apellido}`;
                    pacienteResultado.dataset.id = data.paciente_id;
                    doctorSelect.value = data.doctor_id;

                    document.getElementById('fecha').value = data.fecha;
                    document.getElementById('hora').value = data.hora;
                    document.getElementById('estado').value = data.estado;
                    document.getElementById('descripcion').value = data.descripcion || "";

                    pacienteInput.disabled = true;
                    buscarPacienteBtn.disabled = true;

                    modalTitle.textContent = "Editar Cita";
                    formularioCita.style.display = 'block';
                }
            })
            .catch(error => console.error('Error al cargar la cita:', error));
    }

    // Evento para eliminar una cita
    tablaCitas.addEventListener('click', function (event) {
        if (event.target.classList.contains('delete')) {
            const citaId = event.target.dataset.id;
            if (confirm("¿Estás seguro de que deseas eliminar esta cita?")) {
                fetch(`../PHP/CitaDAO.php`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${citaId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cita eliminada correctamente');
                        location.reload();
                    } else {
                        alert('Hubo un problema al eliminar la cita.');
                    }
                })
                .catch(error => console.error('Error al eliminar la cita:', error));
            }
        }
    });

    // Guardar el ID de la cita seleccionada al hacer clic en el radio button
    tablaCitas.addEventListener('change', function (event) {
        const target = event.target;
        if (target.classList.contains('seleccionar-cita')) {
            selectedCitaId = target.value;
        }
    });
});
