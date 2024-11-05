let currentYear = new Date().getFullYear();
let currentMonth = new Date().getMonth();
let selectedCitaId = null;

document.addEventListener('DOMContentLoaded', function() {
    cargarDoctores();
    populateYearSelect();
    populateCalendar();
});

function populateYearSelect() {
    const yearSelect = document.getElementById('yearSelect');
    for (let i = currentYear - 10; i <= currentYear + 10; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.text = i;
        if (i === currentYear) option.selected = true;
        yearSelect.appendChild(option);
    }
}

function populateCalendar() {
    const daysContainer = document.getElementById('calendar-days');
    daysContainer.innerHTML = ''; // Limpiar los días previos
    document.getElementById('monthSelect').value = currentMonth;
    document.getElementById('yearSelect').value = currentYear;

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

    // Agregar celdas vacías para alinear el primer día del mes correctamente
    for (let i = 0; i < firstDayOfMonth; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('day-cell', 'empty');
        daysContainer.appendChild(emptyCell);
    }

    fetchCitas().then(citas => {
        for (let i = 1; i <= daysInMonth; i++) {
            const dayCell = document.createElement('div');
            dayCell.classList.add('day-cell');
            dayCell.innerHTML = `<span>${i}</span>`;
            dayCell.onclick = () => selectDay(dayCell);

            const citasDelDia = citas.filter(cita => {
                const [year, month, day] = cita.fecha.split('-').map(Number);
                return day === i && (month - 1) === currentMonth && year === currentYear;
            });

            citasDelDia.forEach(cita => {
                const citaDiv = document.createElement('div');
                citaDiv.classList.add('cita');
                citaDiv.textContent = `${cita.paciente_nombre} - ${cita.hora}`;
                citaDiv.onclick = (e) => {
                    e.stopPropagation();
                    showCitaModal(cita);
                };
                dayCell.appendChild(citaDiv);
            });

            daysContainer.appendChild(dayCell);
        }
    });
}

function selectDay(cell) {
    const selected = document.querySelector('.day-cell.selected');
    if (selected) selected.classList.remove('selected');
    cell.classList.add('selected');
}

async function fetchCitas() {
    try {
        const response = await fetch('PHP/cargarCitas.php');
        if (!response.ok) throw new Error('Error al cargar citas');
        return await response.json();
    } catch (error) {
        console.error(error);
        return [];
    }
}

function showCitaModal(cita) {
    selectedCitaId = cita.id;

    document.getElementById('paciente').value = `${cita.paciente_nombre} ${cita.paciente_apellido}`;
    document.getElementById('doctor').value = cita.doctor_id;
    document.getElementById('fecha').value = cita.fecha;
    document.getElementById('hora').value = cita.hora;
    document.getElementById('estado').value = cita.estado;
    document.getElementById('descripcion').value = cita.descripcion;

    document.getElementById('detailPaciente').textContent = `${cita.paciente_nombre} ${cita.paciente_apellido}`;
    document.getElementById('detailDoctor').textContent = cita.doctor_nombre;
    document.getElementById('detailFecha').textContent = cita.fecha;
    document.getElementById('detailHora').textContent = cita.hora;
    document.getElementById('detailEstado').textContent = cita.estado;
    document.getElementById('detailDescripcion').textContent = cita.descripcion;
}

async function cargarDoctores() {
    try {
        const response = await fetch('PHP/cargarCitas.php?action=obtenerDoctores');
        const doctores = await response.json();

        const doctorSelect = document.getElementById('doctor');
        doctorSelect.innerHTML = '';

        doctores.forEach(doctor => {
            const option = document.createElement('option');
            option.value = doctor.id;
            option.textContent = doctor.nombre;
            doctorSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar doctores:', error);
    }
}

async function saveCita(event) {
    event.preventDefault();

    if (!selectedCitaId) {
        alert("Seleccione una cita para editar.");
        return;
    }

    const citaData = {
        id: selectedCitaId,
        doctor: document.getElementById('doctor').value,
        fecha: document.getElementById('fecha').value,
        hora: document.getElementById('hora').value,
        estado: document.getElementById('estado').value,
        descripcion: document.getElementById('descripcion').value,
    };

    try {
        const response = await fetch('PHP/cargarCitas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(citaData)
        });

        const result = await response.json();
        if (result.success) {
            alert('Cita guardada con éxito');
            populateCalendar(); // Recargar el calendario con los cambios
        } else {
            alert('Error al guardar la cita');
        }
    } catch (error) {
        console.error('Error al guardar la cita:', error);
        alert('Error al conectar con el servidor');
    }
}

function previousMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    populateCalendar();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    populateCalendar();
}

function changeMonth() {
    currentMonth = parseInt(document.getElementById('monthSelect').value);
    populateCalendar();
}

function changeYear() {
    currentYear = parseInt(document.getElementById('yearSelect').value);
    populateCalendar();
}
