<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/estadisticasController.php';

$is_component_mode = isset($_GET['modo']) && $_GET['modo'] === 'componente';

if (!$is_component_mode) {
    $auth = new AuthController($conn);
    $auth->checkAuth();
    $page_title = 'Estadísticas';
    include 'objects/header.php';
}

$estadisticasController = new EstadisticasController($conn);
$datos = $estadisticasController->obtenerEstadisticas();
?>

<div class="container mt-4">

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total de Alumnos</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $datos['total_alumnos']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total de Carreras</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $datos['total_carreras']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total de Grupos</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $datos['total_grupos']; ?></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
             <div class="card text-white bg-dark mb-3">
                 <div class="card-header">Tasa de Asistencia General</div>
                 <div class="card-body">
                     <h4 class="card-title"><?php echo $datos['tasa_asistencia']; ?>%</h4>
                     <div class="progress" style="height: 20px;">
                         <div class="progress-bar" role="progressbar" style="width: <?php echo $datos['tasa_asistencia']; ?>%;" aria-valuenow="<?php echo $datos['tasa_asistencia']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                     </div>
                 </div>
             </div>
        </div>
    </div>

    <!-- BOTONES DE EXPORTACIÓN -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <button class="btn btn-success btn-lg mx-2" onclick="exportarTodoA_CSV('reporte_estadisticas_completo.csv')">
                <i class="fas fa-file-excel"></i> Exportar Datos a Excel
            </button>
            <button id="exportPdfBtn" class="btn btn-danger btn-lg mx-2" onclick="exportarTodoA_PDF('reporte_graficas.pdf')">
                <i class="fas fa-file-pdf"></i> Exportar Gráficas a PDF
            </button>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Seguimientos por Estatus</div>
                <div class="card-body">
                    <canvas id="seguimientosPorEstatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Seguimientos por Tipo</div>
                <div class="card-body">
                    <canvas id="seguimientosPorTipoChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Distribución de Alumnos por Estatus</div>
                <div class="card-body">
                    <canvas id="alumnosPorEstatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos y librerías necesarias -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Librerías necesarias para exportar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://unpkg.com/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script id="estadisticas-script">
const alumnosPorCarrera = <?php echo json_encode($datos['alumnos_por_carrera']); ?>;
const alumnosPorEstatus = <?php echo json_encode($datos['alumnos_por_estatus']); ?>;
const gruposPorModalidad = <?php echo json_encode($datos['grupos_por_modalidad']); ?>;
const seguimientosPorEstatus = <?php echo json_encode($datos['seguimientos_por_estatus']); ?>;
const seguimientosPorTipo = <?php echo json_encode($datos['seguimientos_por_tipo']); ?>;

// --- FUNCIÓN DE EXPORTACIÓN GLOBAL A CSV ---
function exportarTodoA_CSV(nombreArchivo) {
    let contenidoCSV = [];
    const procesarDataSet = (titulo, datos, cabeceras) => {
        if (!datos || datos.length === 0) return;
        contenidoCSV.push(titulo);
        contenidoCSV.push(cabeceras.join(','));
        datos.forEach(fila => {
            const valores = Object.values(fila).map(valor => {
                const valorString = String(valor).replace(/"/g, '""');
                return `"${valorString}"`;
            });
            contenidoCSV.push(valores.join(','));
        });
        contenidoCSV.push('');
    };
    procesarDataSet('Seguimientos por Estatus', seguimientosPorEstatus, ['Estatus de Seguimiento', 'Total']);
    procesarDataSet('Seguimientos por Tipo', seguimientosPorTipo, ['Tipo de Seguimiento', 'Total']);
    procesarDataSet('Alumnos por Estatus', alumnosPorEstatus, ['Estatus de Alumno', 'Total']);
    procesarDataSet('Alumnos por Carrera', alumnosPorCarrera, ['Carrera', 'Total de Alumnos']);
    procesarDataSet('Grupos por Modalidad', gruposPorModalidad, ['Modalidad', 'Total de Grupos']);
    if (contenidoCSV.length === 0) {
        Swal.fire('Atención', 'No hay datos para exportar.', 'warning');
        return;
    }
    const csvFinal = contenidoCSV.join('\n');
    const blob = new Blob([csvFinal], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    if (link.download !== undefined) { 
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", nombreArchivo);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// --- FUNCIÓN DE EXPORTACIÓN A PDF ---
async function exportarTodoA_PDF(nombreArchivo) {
    if (!window.jspdf) {
        Swal.fire('Error', 'La librería jsPDF no está cargada.', 'error');
        return;
    }

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const margin = 15;

    let yPosition = margin;
    const exportButton = document.getElementById('exportPdfBtn');
    exportButton.disabled = true;
    exportButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';

    const chartsToExport = [
        { id: 'seguimientosPorEstatusChart', title: 'Seguimientos por Estatus' },
        { id: 'seguimientosPorTipoChart', title: 'Seguimientos por Tipo' },
        { id: 'alumnosPorEstatusChart', title: 'Distribución de Alumnos por Estatus' }
    ];

    for (let i = 0; i < chartsToExport.length; i++) {
        const chartInfo = chartsToExport[i];
        const chartElement = document.getElementById(chartInfo.id);

        if (chartElement) {
            try {
                const canvas = await html2canvas(chartElement, { backgroundColor: '#ffffff' });
                const imgData = canvas.toDataURL('image/png');
                
                const imgWidth = pdfWidth - margin * 2;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                if (i > 0) { 
                    pdf.addPage();
                }
                
                pdf.setFontSize(16);
                pdf.text(chartInfo.title, margin, margin);
                pdf.addImage(imgData, 'PNG', margin, margin + 10, imgWidth, imgHeight);

            } catch (error) {
                console.error('Error al renderizar la gráfica:', chartInfo.id, error);
            }
        }
    }

    pdf.save(nombreArchivo);

    exportButton.disabled = false;
    exportButton.innerHTML = '<i class="fas fa-file-pdf"></i> Exportar Gráficas a PDF';
}

// --- GRÁFICAS ---
new Chart(document.getElementById('alumnosPorEstatusChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: alumnosPorEstatus.map(item => item.estatus_nombre),
        datasets: [{
            data: alumnosPorEstatus.map(item => item.total),
            backgroundColor: ['rgba(40, 167, 69, 0.7)', 'rgba(220, 53, 69, 0.7)', 'rgba(255, 193, 7, 0.7)', 'rgba(108, 117, 125, 0.7)'],
            borderColor: ['#fff'],
            borderWidth: 2
        }]
    }
});

new Chart(document.getElementById('seguimientosPorEstatusChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: seguimientosPorEstatus.map(item => item.estatus_nombre),
        datasets: [{
            data: seguimientosPorEstatus.map(item => item.total),
            backgroundColor: ['rgba(255, 99, 132, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)'],
            borderColor: ['#fff'],
            borderWidth: 2
        }]
    }
});

new Chart(document.getElementById('seguimientosPorTipoChart').getContext('2d'), {
    type: 'polarArea',
    data: {
        labels: seguimientosPorTipo.map(item => item.nombre),
        datasets: [{
            label: 'Número de Seguimientos',
            data: seguimientosPorTipo.map(item => item.total),
            backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)'],
        }]
    }
});
</script>

<?php
    require_once 'objects/footer.php';
?>
