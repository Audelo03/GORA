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
    <!-- Dashboard Principal -->
    <div class="row mb-4">
        <div class="col-12">
          
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="card-title h4 mb-1"><?php echo $datos['total_alumnos']; ?></div>
                        <div class="card-text">Total de Alumnos</div>
                        <small class="text-light"><?php echo $datos['estadisticas_generales']['alumnos_activos']; ?> activos</small>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-people-fill fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="card-title h4 mb-1"><?php echo $datos['total_carreras']; ?></div>
                        <div class="card-text">Carreras</div>
                        <small class="text-light"><?php echo $datos['estadisticas_generales']['total_grupos']; ?> grupos</small>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-mortarboard-fill fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="card-title h4 mb-1"><?php echo $datos['estadisticas_generales']['usuarios_activos']; ?></div>
                        <div class="card-text">Usuarios Activos</div>
                        <small class="text-light"><?php echo $datos['estadisticas_generales']['seguimientos_abiertos']; ?> seguimientos abiertos</small>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-person-check-fill fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="card-title h4 mb-1"><?php echo $datos['tasa_asistencia']; ?>%</div>
                        <div class="card-text">Tasa de Asistencia</div>
                        <small class="text-light"><?php echo $datos['estadisticas_generales']['asistencias_hoy']; ?> asistencias hoy</small>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-calendar-check-fill fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Secundarias -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-graph-up"></i> Actividad Reciente
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-primary"><?php echo $datos['estadisticas_generales']['asistencias_semana']; ?></h5>
                            <small class="text-muted">Asistencias esta semana</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-success"><?php echo $datos['estadisticas_generales']['seguimientos_mes']; ?></h5>
                            <small class="text-muted">Seguimientos este mes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-speedometer2"></i> Tasa de Asistencia General
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-1"><?php echo $datos['tasa_asistencia']; ?>%</h3>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-gradient" role="progressbar" 
                                     style="width: <?php echo $datos['tasa_asistencia']; ?>%;" 
                                     aria-valuenow="<?php echo $datos['tasa_asistencia']; ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?php echo $datos['tasa_asistencia']; ?>%
                                </div>
                            </div>
                        </div>
                     
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTONES DE EXPORTACIÓN -->
     <?php if (!$is_component_mode): ?>
    <div class="row mt-4">
        <div class="col-12 text-center">
            <button class="btn btn-success btn-lg mx-2" onclick="exportarTodoA_Excel('reporte_estadisticas_completo.xlsx')">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </button>
            <button class="btn btn-info btn-lg mx-2" onclick="exportarTodoA_CSV('reporte_estadisticas_completo.csv')">
                <i class="fas fa-file-csv"></i> Exportar a CSV
            </button>
            <button id="exportPdfBtn" class="btn btn-danger btn-lg mx-2" onclick="exportarTodoA_PDF('reporte_graficas.pdf')">
                <i class="fas fa-file-pdf"></i> Exportar Gráficas a PDF
            </button>
            <button class="btn btn-warning btn-lg mx-2" onclick="exportarGraficasComoImagenes()">
                <i class="fas fa-image"></i> Exportar Gráficas como PNG
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Gráficas Principales -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-pie-chart"></i> Distribución de Alumnos por Estatus
                </div>
                <div class="card-body">
                    <canvas id="alumnosPorEstatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-people"></i> Usuarios por Nivel
                </div>
                <div class="card-body">
                    <canvas id="usuariosPorNivelChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas de Seguimientos -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-graph-up"></i> Seguimientos por Estatus
                </div>
                <div class="card-body">
                    <canvas id="seguimientosPorEstatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <i class="bi bi-list-check"></i> Seguimientos por Tipo
                </div>
                <div class="card-body">
                    <canvas id="seguimientosPorTipoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas de Tendencias Temporales -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-calendar-month"></i> Asistencia por Mes (Últimos 12 meses)
                </div>
                <div class="card-body">
                    <canvas id="asistenciaPorMesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-graph-up-arrow"></i> Seguimientos por Mes (Últimos 12 meses)
                </div>
                <div class="card-body">
                    <canvas id="seguimientosPorMesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas de Distribución -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-mortarboard"></i> Carreras Más Populares
                </div>
                <div class="card-body">
                    <canvas id="carrerasMasPopularesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-purple text-white" style="background-color: #6f42c1 !important;">
                    <i class="bi bi-book"></i> Modalidades Más Utilizadas
                </div>
                <div class="card-body">
                    <canvas id="modalidadesMasUtilizadasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas de Productividad -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-teal text-white" style="background-color: #20c997 !important;">
                    <i class="bi bi-person-workspace"></i> Productividad de Tutores
                </div>
                <div class="card-body">
                    <canvas id="productividadTutoresChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-orange text-white" style="background-color: #fd7e14 !important;">
                    <i class="bi bi-calendar-range"></i> Alumnos por Año de Ingreso
                </div>
                <div class="card-body">
                    <canvas id="alumnosPorAnioIngresoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productividad de Tutores -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(45deg, #007bff, #0056b3) !important;">
                    <i class="bi bi-table"></i> Detalle de Productividad de Tutores
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tutor</th>
                                    <th>Grupos Asignados</th>
                                    <th>Alumnos Tutoreados</th>
                                    <th>Seguimientos Realizados</th>
                                    <th>Promedio por Alumno</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datos['productividad_tutores'] as $tutor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tutor['tutor']); ?></td>
                                    <td><span class="badge bg-primary"><?php echo $tutor['grupos_asignados']; ?></span></td>
                                    <td><span class="badge bg-success"><?php echo $tutor['alumnos_tutoreados']; ?></span></td>
                                    <td><span class="badge bg-info"><?php echo $tutor['seguimientos_realizados']; ?></span></td>
                                    <td><span class="badge bg-warning"><?php echo $tutor['promedio_seguimientos_por_alumno']; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script id="estadisticas-script">
// Datos existentes
const alumnosPorCarrera = <?php echo json_encode($datos['alumnos_por_carrera']); ?>;
const alumnosPorEstatus = <?php echo json_encode($datos['alumnos_por_estatus']); ?>;
const gruposPorModalidad = <?php echo json_encode($datos['grupos_por_modalidad']); ?>;
const seguimientosPorEstatus = <?php echo json_encode($datos['seguimientos_por_estatus']); ?>;
const seguimientosPorTipo = <?php echo json_encode($datos['seguimientos_por_tipo']); ?>;

// Nuevos datos
const usuariosPorNivel = <?php echo json_encode($datos['usuarios_por_nivel']); ?>;
const alumnosPorGrupo = <?php echo json_encode($datos['alumnos_por_grupo']); ?>;
const asistenciaPorMes = <?php echo json_encode($datos['asistencia_por_mes']); ?>;
const seguimientosPorMes = <?php echo json_encode($datos['seguimientos_por_mes']); ?>;
const productividadTutores = <?php echo json_encode($datos['productividad_tutores']); ?>;
const alumnosPorAnioIngreso = <?php echo json_encode($datos['alumnos_por_anio_ingreso']); ?>;
const carrerasMasPopulares = <?php echo json_encode($datos['carreras_mas_populares']); ?>;
const modalidadesMasUtilizadas = <?php echo json_encode($datos['modalidades_mas_utilizadas']); ?>;

// --- FUNCIÓN DE EXPORTACIÓN GLOBAL A CSV/EXCEL ---
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
    
    // Datos existentes
    procesarDataSet('Seguimientos por Estatus', seguimientosPorEstatus, ['Estatus de Seguimiento', 'Total']);
    procesarDataSet('Seguimientos por Tipo', seguimientosPorTipo, ['Tipo de Seguimiento', 'Total']);
    procesarDataSet('Alumnos por Estatus', alumnosPorEstatus, ['Estatus de Alumno', 'Total']);
    procesarDataSet('Alumnos por Carrera', alumnosPorCarrera, ['Carrera', 'Total de Alumnos']);
    procesarDataSet('Grupos por Modalidad', gruposPorModalidad, ['Modalidad', 'Total de Grupos']);
    
    // Nuevos datos
    procesarDataSet('Usuarios por Nivel', usuariosPorNivel, ['Nivel de Usuario', 'Total']);
    procesarDataSet('Alumnos por Grupo', alumnosPorGrupo, ['Grupo', 'Carrera', 'Total de Alumnos']);
    procesarDataSet('Asistencia por Mes', asistenciaPorMes, ['Mes', 'Total Registros', 'Asistencias', 'Faltas', 'Porcentaje Asistencia']);
    procesarDataSet('Seguimientos por Mes', seguimientosPorMes, ['Mes', 'Total Seguimientos', 'Abiertos', 'En Progreso', 'Cerrados']);
    procesarDataSet('Productividad de Tutores', productividadTutores, ['Tutor', 'Grupos Asignados', 'Alumnos Tutoreados', 'Seguimientos Realizados', 'Promedio por Alumno']);
    procesarDataSet('Alumnos por Año de Ingreso', alumnosPorAnioIngreso, ['Año de Ingreso', 'Total Alumnos', 'Activos', 'Egresados', 'Bajas']);
    procesarDataSet('Carreras Más Populares', carrerasMasPopulares, ['Carrera', 'Total Alumnos', 'Total Grupos', 'Promedio Alumnos por Grupo']);
    procesarDataSet('Modalidades Más Utilizadas', modalidadesMasUtilizadas, ['Modalidad', 'Total Grupos', 'Total Alumnos', 'Promedio Alumnos por Grupo']);
    
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
        { id: 'alumnosPorEstatusChart', title: 'Distribución de Alumnos por Estatus' },
        { id: 'usuariosPorNivelChart', title: 'Usuarios por Nivel' },
        { id: 'seguimientosPorEstatusChart', title: 'Seguimientos por Estatus' },
        { id: 'seguimientosPorTipoChart', title: 'Seguimientos por Tipo' },
        { id: 'asistenciaPorMesChart', title: 'Asistencia por Mes' },
        { id: 'seguimientosPorMesChart', title: 'Seguimientos por Mes' },
        { id: 'carrerasMasPopularesChart', title: 'Carreras Más Populares' },
        { id: 'modalidadesMasUtilizadasChart', title: 'Modalidades Más Utilizadas' },
        { id: 'productividadTutoresChart', title: 'Productividad de Tutores' },
        { id: 'alumnosPorAnioIngresoChart', title: 'Alumnos por Año de Ingreso' }
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

// --- FUNCIÓN DE EXPORTACIÓN A EXCEL ---
function exportarTodoA_Excel(nombreArchivo) {
    if (!window.XLSX) {
        Swal.fire('Error', 'La librería XLSX no está cargada.', 'error');
        return;
    }

    const workbook = XLSX.utils.book_new();
    
    // Función para crear una hoja de datos
    const crearHoja = (nombreHoja, datos, cabeceras) => {
        if (!datos || datos.length === 0) return;
        
        const datosConCabeceras = [cabeceras, ...datos.map(fila => 
            cabeceras.map(cabecera => {
                const valor = fila[cabecera.toLowerCase().replace(/\s+/g, '_')] || 
                             fila[cabecera] || 
                             Object.values(fila)[cabeceras.indexOf(cabecera)] || '';
                return valor;
            })
        )];
        
        const worksheet = XLSX.utils.aoa_to_sheet(datosConCabeceras);
        XLSX.utils.book_append_sheet(workbook, worksheet, nombreHoja);
    };

    // Crear hojas para cada conjunto de datos
    crearHoja('Alumnos por Estatus', alumnosPorEstatus, ['Estatus', 'Total']);
    crearHoja('Usuarios por Nivel', usuariosPorNivel, ['Nivel', 'Total']);
    crearHoja('Seguimientos por Estatus', seguimientosPorEstatus, ['Estatus', 'Total']);
    crearHoja('Seguimientos por Tipo', seguimientosPorTipo, ['Tipo', 'Total']);
    crearHoja('Alumnos por Carrera', alumnosPorCarrera, ['Carrera', 'Total']);
    crearHoja('Grupos por Modalidad', gruposPorModalidad, ['Modalidad', 'Total']);
    crearHoja('Alumnos por Grupo', alumnosPorGrupo, ['Grupo', 'Carrera', 'Total']);
    crearHoja('Asistencia por Mes', asistenciaPorMes, ['Mes', 'Total Registros', 'Asistencias', 'Faltas', 'Porcentaje']);
    crearHoja('Seguimientos por Mes', seguimientosPorMes, ['Mes', 'Total', 'Abiertos', 'En Progreso', 'Cerrados']);
    crearHoja('Productividad Tutores', productividadTutores, ['Tutor', 'Grupos', 'Alumnos', 'Seguimientos', 'Promedio']);
    crearHoja('Alumnos por Año', alumnosPorAnioIngreso, ['Año', 'Total', 'Activos', 'Egresados', 'Bajas']);
    crearHoja('Carreras Populares', carrerasMasPopulares, ['Carrera', 'Alumnos', 'Grupos', 'Promedio']);
    crearHoja('Modalidades', modalidadesMasUtilizadas, ['Modalidad', 'Grupos', 'Alumnos', 'Promedio']);

    // Crear hoja de resumen
    const resumenData = [
        ['Métrica', 'Valor'],
        ['Total Alumnos', <?php echo $datos['total_alumnos']; ?>],
        ['Total Carreras', <?php echo $datos['total_carreras']; ?>],
        ['Total Grupos', <?php echo $datos['total_grupos']; ?>],
        ['Tasa de Asistencia', '<?php echo $datos['tasa_asistencia']; ?>%'],
        ['Alumnos Activos', <?php echo $datos['estadisticas_generales']['alumnos_activos']; ?>],
        ['Usuarios Activos', <?php echo $datos['estadisticas_generales']['usuarios_activos']; ?>],
        ['Seguimientos Abiertos', <?php echo $datos['estadisticas_generales']['seguimientos_abiertos']; ?>],
        ['Asistencias Hoy', <?php echo $datos['estadisticas_generales']['asistencias_hoy']; ?>],
        ['Asistencias Esta Semana', <?php echo $datos['estadisticas_generales']['asistencias_semana']; ?>],
        ['Seguimientos Este Mes', <?php echo $datos['estadisticas_generales']['seguimientos_mes']; ?>]
    ];
    
    const resumenSheet = XLSX.utils.aoa_to_sheet(resumenData);
    XLSX.utils.book_append_sheet(workbook, resumenSheet, 'Resumen General');

    // Exportar archivo
    XLSX.writeFile(workbook, nombreArchivo);
    
    Swal.fire({
        icon: 'success',
        title: '¡Exportación Exitosa!',
        text: 'El archivo Excel se ha generado correctamente.',
        timer: 2000,
        showConfirmButton: false
    });
}

// --- FUNCIÓN DE EXPORTACIÓN DE GRÁFICAS COMO IMÁGENES ---
async function exportarGraficasComoImagenes() {
    const chartsToExport = [
        { id: 'alumnosPorEstatusChart', title: 'Distribución de Alumnos por Estatus' },
        { id: 'usuariosPorNivelChart', title: 'Usuarios por Nivel' },
        { id: 'seguimientosPorEstatusChart', title: 'Seguimientos por Estatus' },
        { id: 'seguimientosPorTipoChart', title: 'Seguimientos por Tipo' },
        { id: 'asistenciaPorMesChart', title: 'Asistencia por Mes' },
        { id: 'seguimientosPorMesChart', title: 'Seguimientos por Mes' },
        { id: 'carrerasMasPopularesChart', title: 'Carreras Más Populares' },
        { id: 'modalidadesMasUtilizadasChart', title: 'Modalidades Más Utilizadas' },
        { id: 'productividadTutoresChart', title: 'Productividad de Tutores' },
        { id: 'alumnosPorAnioIngresoChart', title: 'Alumnos por Año de Ingreso' }
    ];

    try {
        for (let i = 0; i < chartsToExport.length; i++) {
            const chartInfo = chartsToExport[i];
            const chartElement = document.getElementById(chartInfo.id);
            
            if (chartElement) {
                const canvas = await html2canvas(chartElement, { 
                    backgroundColor: '#ffffff',
                    scale: 2,
                    logging: false
                });
                
                const link = document.createElement('a');
                link.download = `${chartInfo.title.replace(/\s+/g, '_')}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                // Pequeña pausa entre descargas
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }
        
        Swal.fire({
            icon: 'success',
            title: '¡Exportación Exitosa!',
            text: 'Todas las gráficas se han descargado como imágenes PNG.',
            timer: 2000,
            showConfirmButton: false
        });
        
    } catch (error) {
        console.error('Error al exportar gráficas:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al exportar las gráficas como imágenes.'
        });
    }
}

// --- CONFIGURACIÓN GLOBAL DE CHART.JS ---
Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#666';

// --- GRÁFICAS PRINCIPALES ---

// 1. Distribución de Alumnos por Estatus
new Chart(document.getElementById('alumnosPorEstatusChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: alumnosPorEstatus.map(item => item.estatus_nombre),
        datasets: [{
            data: alumnosPorEstatus.map(item => item.total),
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',   // Verde para Activos
                'rgba(220, 53, 69, 0.8)',   // Rojo para Inactivos
                'rgba(255, 193, 7, 0.8)',   // Amarillo para Egresados
                'rgba(108, 117, 125, 0.8)'  // Gris para Bajas
            ],
            borderColor: ['#fff'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// 2. Usuarios por Nivel
if (usuariosPorNivel && usuariosPorNivel.length > 0) {
    new Chart(document.getElementById('usuariosPorNivelChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: usuariosPorNivel.map(item => item.nombre),
            datasets: [{
                data: usuariosPorNivel.map(item => item.total),
            backgroundColor: [
                'rgba(0, 123, 255, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: ['#fff'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
    });
} else {
    document.getElementById('usuariosPorNivelChart').parentElement.innerHTML = '<div class="text-center text-muted p-4">No hay datos disponibles</div>';
}

// 3. Seguimientos por Estatus
new Chart(document.getElementById('seguimientosPorEstatusChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: seguimientosPorEstatus.map(item => item.estatus_nombre),
        datasets: [{
            label: 'Seguimientos',
            data: seguimientosPorEstatus.map(item => item.total),
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// 4. Seguimientos por Tipo
new Chart(document.getElementById('seguimientosPorTipoChart').getContext('2d'), {
    type: 'polarArea',
    data: {
        labels: seguimientosPorTipo.map(item => item.nombre),
        datasets: [{
            label: 'Número de Seguimientos',
            data: seguimientosPorTipo.map(item => item.total),
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// 5. Asistencia por Mes
new Chart(document.getElementById('asistenciaPorMesChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: asistenciaPorMes.map(item => item.mes),
        datasets: [{
            label: 'Porcentaje de Asistencia',
            data: asistenciaPorMes.map(item => item.porcentaje_asistencia),
            borderColor: 'rgba(0, 123, 255, 1)',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// 6. Seguimientos por Mes
new Chart(document.getElementById('seguimientosPorMesChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: seguimientosPorMes.map(item => item.mes),
        datasets: [
            {
                label: 'Abiertos',
                data: seguimientosPorMes.map(item => item.abiertos),
                backgroundColor: 'rgba(255, 99, 132, 0.8)'
            },
            {
                label: 'En Progreso',
                data: seguimientosPorMes.map(item => item.en_progreso),
                backgroundColor: 'rgba(255, 206, 86, 0.8)'
            },
            {
                label: 'Cerrados',
                data: seguimientosPorMes.map(item => item.cerrados),
                backgroundColor: 'rgba(75, 192, 192, 0.8)'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                stacked: true
            },
            x: {
                stacked: true
            }
        }
    }
});

// 7. Carreras Más Populares
new Chart(document.getElementById('carrerasMasPopularesChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: carrerasMasPopulares.map(item => item.carrera.length > 20 ? item.carrera.substring(0, 20) + '...' : item.carrera),
        datasets: [{
            label: 'Total de Alumnos',
            data: carrerasMasPopulares.map(item => item.total_alumnos),
            backgroundColor: 'rgba(220, 53, 69, 0.8)',
            borderColor: 'rgba(220, 53, 69, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// 8. Modalidades Más Utilizadas
new Chart(document.getElementById('modalidadesMasUtilizadasChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: modalidadesMasUtilizadas.map(item => item.modalidad),
        datasets: [{
            label: 'Total de Grupos',
            data: modalidadesMasUtilizadas.map(item => item.total_grupos),
            backgroundColor: 'rgba(111, 66, 193, 0.8)',
            borderColor: 'rgba(111, 66, 193, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// 9. Productividad de Tutores
new Chart(document.getElementById('productividadTutoresChart').getContext('2d'), {
    type: 'scatter',
    data: {
        datasets: [{
            label: 'Tutores',
            data: productividadTutores.map(item => ({
                x: item.alumnos_tutoreados,
                y: item.seguimientos_realizados
            })),
            backgroundColor: 'rgba(32, 201, 151, 0.8)',
            borderColor: 'rgba(32, 201, 151, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Alumnos Tutoreados'
                },
                beginAtZero: true
            },
            y: {
                title: {
                    display: true,
                    text: 'Seguimientos Realizados'
                },
                beginAtZero: true
            }
        }
    }
});

// 10. Alumnos por Año de Ingreso
new Chart(document.getElementById('alumnosPorAnioIngresoChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: alumnosPorAnioIngreso.map(item => item.anio_ingreso),
        datasets: [
            {
                label: 'Activos',
                data: alumnosPorAnioIngreso.map(item => item.activos),
                backgroundColor: 'rgba(40, 167, 69, 0.8)'
            },
            {
                label: 'Egresados',
                data: alumnosPorAnioIngreso.map(item => item.egresados),
                backgroundColor: 'rgba(255, 193, 7, 0.8)'
            },
            {
                label: 'Bajas',
                data: alumnosPorAnioIngreso.map(item => item.bajas),
                backgroundColor: 'rgba(220, 53, 69, 0.8)'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                stacked: true
            },
            x: {
                stacked: true
            }
        }
    }
});
</script>

<?php
    require_once 'objects/footer.php';
?>

