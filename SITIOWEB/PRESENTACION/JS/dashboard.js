document.addEventListener("DOMContentLoaded", () => {
    // Mostrar fecha actual en el formato DD/MM/YYYY
    const fechaElement = document.getElementById("fecha");
    const hoy = new Date();
    
    const dia = String(hoy.getDate()).padStart(2, '0');
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const año = hoy.getFullYear();
    
    const fechaActual = `${dia}/${mes}/${año}`;
    fechaElement.textContent = fechaActual;

    // Cargar datos del dashboard
    cargarUsuarios();
    cargarPacientes();
    cargarProximasCitas();
});

// Función para cargar el número de usuarios
function cargarUsuarios() {
    fetch('PHP/dashboard_data.php?action=usuarios')
        .then(response => response.ok ? response.json() : Promise.reject('Error en la solicitud de usuarios'))
        .then(data => {
            if (data && data.total_usuarios !== undefined) {
                document.getElementById("usuarios").textContent = data.total_usuarios;
            }
        })
        .catch(error => console.error(error));
}

// Función para cargar el número de pacientes
function cargarPacientes() {
    fetch('PHP/dashboard_data.php?action=pacientes')
        .then(response => response.ok ? response.json() : Promise.reject('Error en la solicitud de pacientes'))
        .then(data => {
            if (data && data.total_pacientes !== undefined) {
                document.getElementById("pacientes").textContent = data.total_pacientes;
            }
        })
        .catch(error => console.error(error));
}

// Función para cargar las próximas citas
function cargarProximasCitas() {
    fetch('PHP/dashboard_data.php?action=citas')
        .then(response => response.ok ? response.json() : Promise.reject('Error en la solicitud de citas'))
        .then(data => {
            const citasContainer = document.querySelector(".appointments-container tbody");
            citasContainer.innerHTML = '';
            data.proximas_citas.forEach(cita => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${cita.nombre}</td>
                    <td>${cita.fecha}</td>
                    <td>${cita.hora}</td>
                    <td>${cita.estado}</td>
                `;
                citasContainer.appendChild(row);
            });
        })
        .catch(error => console.error(error));
}

// Función para buscar paciente por DNI y mostrar detalles de la próxima cita si existe
function buscarPaciente() {
    const dni = document.getElementById("dni-input").value;

    fetch(`PHP/dashboard_data.php?action=buscarPaciente&dni=${dni}`)
        .then(response => response.ok ? response.json() : Promise.reject('Error en la solicitud de búsqueda de paciente'))
        .then(data => {
            const pacienteInfoContainer = document.getElementById("paciente-info-container");
            const nombrePaciente = document.getElementById("nombre-paciente");
            const apellidoPaciente = document.getElementById("apellido-paciente");
            const citaPaciente = document.getElementById("cita-paciente");

            // Verificar que el contenedor exista antes de modificar su estilo
            if (pacienteInfoContainer) {
                if (data && data.paciente) {
                    // Mostrar los datos del paciente
                    nombrePaciente.textContent = data.paciente.nombre;
                    apellidoPaciente.textContent = data.paciente.apellido;
                    
                    // Verificar si el paciente tiene una próxima cita
                    if (data.paciente.cita) {
                        citaPaciente.textContent = `Próxima Cita: ${data.paciente.cita.fecha} a las ${data.paciente.cita.hora} (${data.paciente.cita.estado})`;
                    } else {
                        citaPaciente.textContent = "Próxima Cita: Sin cita programada";
                    }
                    
                    // Mostrar la sección de información del paciente
                    pacienteInfoContainer.style.display = "block";
                } else {
                    // Si no se encuentra el paciente, mostrar un mensaje en su lugar
                    pacienteInfoContainer.style.display = "block";
                    nombrePaciente.textContent = "Paciente no encontrado";
                    apellidoPaciente.textContent = "";
                    citaPaciente.textContent = "";
                }
            } else {
                console.error("El contenedor de información del paciente no existe en el DOM.");
            }
        })
        .catch(error => console.error(error));
}
