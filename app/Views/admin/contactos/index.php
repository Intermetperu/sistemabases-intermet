<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<style>
    :root {
        --ctc-border: #e7e9ee;
        --ctc-muted-bg: #f6f7fb;
        --ctc-accent: #4a5cf0;
        --ctc-accent-soft: #eef0fe;
        --ctc-radius: 10px;
    }

    .ctc-card {
        border: 1px solid var(--ctc-border);
        border-radius: var(--ctc-radius);
        box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
    }

    .ctc-card .card-body {
        padding: 1.35rem;
    }

    .ctc-section-title {
        font-size: 1.02rem;
        font-weight: 600;
        color: #1c2033;
        margin-bottom: 0;
    }

    .ctc-count-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: var(--ctc-accent-soft);
        color: var(--ctc-accent);
        font-weight: 600;
        font-size: .78rem;
        padding: .18rem .6rem;
        border-radius: 999px;
        margin-left: .5rem;
        vertical-align: middle;
    }

    .ctc-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }

    .ctc-toolbar .btn {
        border-radius: 8px;
        font-size: .84rem;
    }

    .ctc-filters {
        background: var(--ctc-muted-bg);
        border: 1px solid var(--ctc-border);
        border-radius: var(--ctc-radius);
        padding: .85rem;
    }

    .ctc-filters .form-label {
        font-size: .72rem;
        text-transform: none;
        color: #6b7280;
        margin-bottom: .25rem;
    }

    #results table thead th,
    #explorerTbody ~ thead th,
    .table.ctc-table thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
        font-size: .78rem;
        color: #4b5266;
        border-bottom: 2px solid var(--ctc-border);
    }

    .ctc-empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #8a90a2;
    }

    .ctc-empty-state i {
        font-size: 2.2rem;
        display: block;
        margin-bottom: .5rem;
        color: #c6cadb;
    }

    .badge-status {
        font-weight: 500;
        font-size: .72rem;
        padding: .3em .55em;
    }

    .ctc-upload-drop {
        border: 1.5px dashed #c9cee0;
        border-radius: var(--ctc-radius);
        background: var(--ctc-muted-bg);
        padding: 1rem;
    }
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Contactos</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class='bx bx-home-alt'></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Contactos</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card ctc-card mb-3">
    <div class="card-body">
        <h5 class="ctc-section-title mb-3"><i class='bx bx-cloud-upload me-1'></i> Cargar archivo CSV</h5>
        <div class="ctc-upload-drop">
            <form id="uploadForm" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Selecciona un archivo (separado por ";" o ",")</label>
                    <input type="file" id="csvFile" accept=".csv" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-upload me-1"></i> Cargar CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card ctc-card">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h5 class="ctc-section-title">
                Búsqueda de registros
                <span class="ctc-count-pill" id="totalContactosLabel"><i class="bx bx-loader-alt bx-spin"></i> cargando...</span>
            </h5>

            <div class="ctc-toolbar">
                <span class="text-muted small" id="tempListCounter">Lista temporal: 0 contactos</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button id="addToTempListButton" class="btn btn-outline-info">
                        <i class="bx bx-list-ul me-1"></i> Agregar a lista
                    </button>
                    <button id="viewTempListButton" class="btn btn-outline-primary">
                        <i class="bx bx-show me-1"></i> Ver lista
                    </button>
                    <button id="downloadCSVButton" class="btn btn-outline-success">
                        <i class="bx bx-file me-1"></i> CSV
                    </button>
                    <button id="downloadExcelButton" class="btn btn-outline-success">
                        <i class="bx bxs-file-export me-1"></i> Excel
                    </button>
                    <button id="clearTempListButton" class="btn btn-outline-danger">
                        <i class="bx bx-trash me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <div class="ctc-toolbar mb-3">
            <div class="btn-group btn-group-sm" role="group">
                <button id="openExplorerButton" class="btn btn-dark">
                    <i class="bx bx-table me-1"></i> Ver toda la data
                </button>
                <button id="openCrearButton" class="btn btn-outline-primary">
                    <i class="bx bx-user-plus me-1"></i> Agregar contacto
                </button>
                <button id="openSegmentarButton" class="btn" style="background:#6f42c1;color:#fff;">
                    <i class="bx bx-filter-alt me-1"></i> Segmentar y exportar
                </button>
            </div>
        </div>

        <!-- Filtros rápidos -->
        <div class="ctc-filters mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">País</label>
                    <select id="filtroPais" class="form-select form-select-sm">
                        <option value="">Todos los países</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Empresa</label>
                    <select id="filtroEmpresa" class="form-control form-control-sm" style="width:100%"></select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="limpiarFiltrosButton" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bx bx-x me-1"></i> Limpiar filtros
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" id="buscarDuplicadosButton" class="btn btn-sm btn-warning w-100">
                        <i class="bx bx-copy-alt me-1"></i> Buscar duplicados
                    </button>
                </div>
            </div>
        </div>

        <form id="searchForm" class="row g-2 mb-3">
            <div class="col-md-9">
                <div class="position-relative">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, empresa, correo, cargo, país, LinkedIn...">
                    <span id="searchSpinner" class="spinner-border spinner-border-sm text-primary position-absolute" style="right:12px; top:12px; display:none;"></span>
                </div>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i></button>
            </div>
            <div class="col-md-2">
                <button type="button" id="deleteButton" disabled class="btn btn-danger w-100">
                    <i class="bx bx-trash me-1"></i> Eliminar
                </button>
            </div>
        </form>

        <div id="message" class="mb-3" style="display:none;"></div>

        <div id="results">
            <div class="ctc-empty-state">
                <i class='bx bx-search-alt'></i>
                Empieza a escribir o usa los filtros para buscar.
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Contacto -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-height: 90vh; margin-top: 5vh; margin-bottom: 5vh;">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title"><i class='bx bx-edit-alt me-1'></i> Editar contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" style="display: flex; flex-direction: column; min-height: 0;">
                <div class="modal-body" style="overflow-y: auto; flex-grow: 1;">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres y Apellidos</label>
                            <input type="text" name="nombres_y_apellidos_completos" id="edit_nombres" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Empresa</label>
                            <input type="text" name="empresa" id="edit_empresa" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cargo</label>
                            <input type="text" name="cargo" id="edit_cargo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Corporativo</label>
                            <input type="email" name="correo_corporativo" id="edit_correo_corporativo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Personal</label>
                            <input type="email" name="correo_electronico" id="edit_correo_electronico" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Celular</label>
                            <input type="text" name="celular" id="edit_celular" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="edit_telefono" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn</label>
                            <input type="url" name="linkedin" id="edit_linkedin" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">País</label>
                            <input type="text" name="pais" id="edit_pais" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Correo</label>
                            <input type="text" name="status_correo_corporativo" id="edit_status_correo" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nota de Origen</label>
                            <textarea name="nota_origen" id="edit_nota" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="flex-shrink: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lista Temporal -->
<div class="modal fade" id="tempListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tempListModalTitle"><i class='bx bx-list-ul me-1'></i> Lista Temporal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle ctc-table">
                        <thead>
                            <tr>
                                <th>Acciones</th>
                                <th>Nombres y Apellidos</th>
                                <th>Empresa</th>
                                <th>Cargo</th>
                                <th>Correo</th>
                                <th>Celular</th>
                            </tr>
                        </thead>
                        <tbody id="tempListBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Duplicados -->
<div class="modal fade" id="duplicadosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-height:90vh; margin-top:5vh; margin-bottom:5vh;">
        <div class="modal-content" style="max-height:90vh; display:flex; flex-direction:column;">
            <div class="modal-header" style="flex-shrink:0;">
                <h5 class="modal-title"><i class='bx bx-copy-alt me-1'></i> Contactos duplicados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y:auto; flex-grow:1;" id="duplicadosBody">
                <div class="ctc-empty-state">
                    <i class='bx bx-loader-alt bx-spin'></i>
                    Buscando duplicados...
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink:0;">
                <span class="text-muted small me-auto" id="duplicadosResumen"></span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="eliminarDuplicadosButton">
                    <i class="bx bx-trash me-1"></i> Eliminar seleccionados
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Explorador de Datos (paginación real, columnas, edición y borrado) -->
<div class="modal fade" id="explorerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class='bx bx-table me-1'></i> Explorador de datos
                    <span class="ctc-count-pill" id="explorerTotalLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="ctc-filters d-flex flex-wrap align-items-end gap-2 mb-3">
                    <div>
                        <label class="form-label small mb-1">Buscar texto</label>
                        <input type="text" id="explorerTerm" class="form-control form-control-sm" style="width:220px" placeholder="nombre, empresa, correo...">
                    </div>
                    <div>
                        <label class="form-label small mb-1">País</label>
                        <select id="explorerPais" class="form-select form-select-sm" style="width:160px">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small mb-1">Empresa</label>
                        <input type="text" id="explorerEmpresa" class="form-control form-control-sm" style="width:180px" placeholder="empresa contiene...">
                    </div>
                    <button type="button" id="explorerBuscarButton" class="btn btn-sm btn-primary">
                        <i class="bx bx-search me-1"></i> Filtrar
                    </button>
                    <button type="button" id="explorerLimpiarButton" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-x me-1"></i> Limpiar
                    </button>

                    <div class="dropdown ms-auto">
                        <button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="bx bx-columns me-1"></i> Columnas
                        </button>
                        <div class="dropdown-menu p-2" id="explorerColumnasMenu" style="max-height:400px; overflow-y:auto; min-width:280px;"></div>
                    </div>

                    <div>
                        <label class="form-label small mb-1">Por página</label>
                        <select id="explorerPerPage" class="form-select form-select-sm" style="width:100px">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="text-muted small" id="explorerSeleccionLabel">0 seleccionados (en todas las páginas)</span>
                    <button type="button" id="explorerAgregarListaButton" class="btn btn-sm btn-info text-white">
                        <i class="bx bx-list-ul me-1"></i> Agregar seleccionados a lista temporal
                    </button>
                    <button type="button" id="explorerEliminarSeleccionadosButton" class="btn btn-sm btn-danger" disabled>
                        <i class="bx bx-trash me-1"></i> Eliminar seleccionados
                    </button>
                    <button type="button" id="explorerLimpiarSeleccionButton" class="btn btn-sm btn-outline-secondary">
                        Deseleccionar todo
                    </button>
                </div>

                <div id="explorerTableWrapper" class="table-responsive">
                    <table class="table table-striped table-hover align-middle table-sm ctc-table">
                        <thead class="table-light" id="explorerThead"></thead>
                        <tbody id="explorerTbody">
                            <tr><td class="text-center text-muted py-4">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>

                <nav class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-muted small" id="explorerPageInfo"></span>
                    <ul class="pagination pagination-sm mb-0" id="explorerPagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal Segmentar y Exportar -->
<div class="modal fade" id="segmentarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-height:90vh; margin-top:5vh; margin-bottom:5vh;">
        <div class="modal-content" style="max-height:90vh; display:flex; flex-direction:column;">
            <div class="modal-header" style="flex-shrink:0;">
                <h5 class="modal-title"><i class='bx bx-filter-alt me-1'></i> Segmentar contactos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y:auto; flex-grow:1;">
                <p class="text-muted small">
                    Arma condiciones para aislar un grupo de contactos (ej: País = Perú, Empresa contiene "Minera", Correo corporativo no vacío).
                    Todas las reglas se combinan con "Y" (deben cumplirse todas).
                </p>

                <div id="segmentarReglas"></div>

                <button type="button" id="segmentarAgregarReglaButton" class="btn btn-sm btn-outline-primary mt-1">
                    <i class="bx bx-plus me-1"></i> Agregar regla
                </button>

                <hr>

                <div class="d-flex align-items-center gap-2 mb-2">
                    <button type="button" id="segmentarPreviewButton" class="btn btn-sm btn-primary">
                        <i class="bx bx-search-alt me-1"></i> Vista previa (contar coincidencias)
                    </button>
                    <span id="segmentarPreviewResultado" class="fw-semibold"></span>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="button" id="segmentarVerExplorerButton" class="btn btn-sm btn-dark">
                        <i class="bx bx-table me-1"></i> Ver resultados en el explorador
                    </button>
                    <button type="button" id="segmentarExportarButton" class="btn btn-sm btn-success">
                        <i class="bx bx-download me-1"></i> Exportar CSV de este segmento
                    </button>
                    <button type="button" id="segmentarEliminarButton" class="btn btn-sm btn-danger ms-auto">
                        <i class="bx bx-trash me-1"></i> Eliminar todos los que coinciden
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Contacto -->
<div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-height: 90vh; margin-top: 5vh; margin-bottom: 5vh;">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title"><i class='bx bx-user-plus me-1'></i> Agregar contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crearForm" style="display: flex; flex-direction: column; min-height: 0;">
                <div class="modal-body" style="overflow-y: auto; flex-grow: 1;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres y Apellidos</label>
                            <input type="text" name="nombres_y_apellidos_completos" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo Documento</label>
                            <input type="text" name="tipo_documento_identidad" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nro Documento</label>
                            <input type="text" name="nro_documento" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Empresa</label>
                            <input type="text" name="empresa" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cargo</label>
                            <input type="text" name="cargo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sector</label>
                            <input type="text" name="sector" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Corporativo</label>
                            <input type="email" name="correo_corporativo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Personal</label>
                            <input type="email" name="correo_electronico" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Celular</label>
                            <input type="text" name="celular" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn</label>
                            <input type="url" name="linkedin" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Web Empresa</label>
                            <input type="text" name="web_empresa" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">País</label>
                            <input type="text" name="pais" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unidad de Negocio</label>
                            <input type="text" name="unidad_negocio" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nota de Origen</label>
                            <textarea name="nota_origen" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observación</label>
                            <textarea name="observacion" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="flex-shrink: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Contacto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    (function() {
        const API = {
            buscar: base_url + 'contactos/buscar',
            csv: base_url + 'contactos/csv',
            actualizar: (id) => base_url + 'contactos/actualizar/' + id,
            eliminar: base_url + 'contactos/eliminar',
            paises: base_url + 'contactos/paises',
            empresasBuscar: base_url + 'contactos/empresas-buscar',
            duplicados: base_url + 'contactos/duplicados',
            listar: base_url + 'contactos/listar',
            columnas: base_url + 'contactos/columnas',
            contarFiltro: base_url + 'contactos/contar-filtro',
            crear: base_url + 'contactos/crear',
            eliminarPorFiltro: base_url + 'contactos/eliminar-por-filtro',
            exportarCsv: base_url + 'contactos/exportar-csv',
        };

        const uploadForm = document.getElementById('uploadForm');
        const csvFile = document.getElementById('csvFile');
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        const searchSpinner = document.getElementById('searchSpinner');
        const messageDiv = document.getElementById('message');
        const resultsDiv = document.getElementById('results');
        const deleteButton = document.getElementById('deleteButton');
        const downloadCSVButton = document.getElementById('downloadCSVButton');
        const downloadExcelButton = document.getElementById('downloadExcelButton');
        const addToTempListButton = document.getElementById('addToTempListButton');
        const clearTempListButton = document.getElementById('clearTempListButton');
        const viewTempListButton = document.getElementById('viewTempListButton');
        const tempListCounter = document.getElementById('tempListCounter');
        const filtroPais = document.getElementById('filtroPais');
        const limpiarFiltrosButton = document.getElementById('limpiarFiltrosButton');
        const buscarDuplicadosButton = document.getElementById('buscarDuplicadosButton');

        const editModalEl = document.getElementById('editModal');
        const editModal = new bootstrap.Modal(editModalEl);
        const editForm = document.getElementById('editForm');

        const tempListModalEl = document.getElementById('tempListModal');
        const tempListModal = new bootstrap.Modal(tempListModalEl);

        const duplicadosModalEl = document.getElementById('duplicadosModal');
        const duplicadosModal = new bootstrap.Modal(duplicadosModalEl);

        let globalResults = [];
        let searchTimeout = null;
        let searchAbortController = null;
        let searchRequestId = 0;

        function showMessage(message, type) {
            messageDiv.className = `alert ${type === 'error' ? 'alert-danger' : 'alert-success'}`;
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 7000);
        }

        function getTempList() {
            return JSON.parse(localStorage.getItem('tempContactsList') || '[]');
        }

        function updateTempList(contacts) {
            localStorage.setItem('tempContactsList', JSON.stringify(contacts));
            updateTempListCounter();
        }

        function updateTempListCounter() {
            tempListCounter.textContent = `Lista temporal: ${getTempList().length} contactos`;
        }

        function clearTempList() {
            Swal.fire({
                title: '¿Limpiar lista temporal?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('tempContactsList');
                    updateTempListCounter();
                    showMessage('Lista temporal limpiada', 'success');
                    if (globalResults.length > 0) displayResults(globalResults);
                }
            });
        }

        function addSelectedToTempList() {
            const selected = document.querySelectorAll('.row-checkbox:checked');
            if (selected.length === 0) {
                showMessage('Seleccione al menos un contacto para agregar a la lista', 'error');
                return;
            }

            const tempList = getTempList();
            const selectedIds = Array.from(selected).map(cb => cb.dataset.contactId);
            const contactsToAdd = globalResults.filter(c =>
                selectedIds.includes(c.id.toString()) && !tempList.some(t => t.id === c.id)
            );

            if (contactsToAdd.length === 0) {
                showMessage('Los contactos seleccionados ya están en la lista temporal', 'error');
                return;
            }

            updateTempList([...tempList, ...contactsToAdd]);
            showMessage(`${contactsToAdd.length} contactos agregados a la lista temporal`, 'success');
            selected.forEach(cb => cb.checked = false);
            updateDeleteButton();
            displayResults(globalResults);
        }

        function removeFromTempList(contactId) {
            const updated = getTempList().filter(c => c.id !== contactId);
            updateTempList(updated);
            renderTempListModal();
            if (globalResults.length > 0) displayResults(globalResults);
            showMessage('Contacto removido de la lista temporal', 'success');
        }
        window.removeFromTempList = removeFromTempList;

        function renderTempListModal() {
            const tempList = getTempList();
            document.getElementById('tempListModalTitle').innerHTML = `<i class='bx bx-list-ul me-1'></i> Lista Temporal (${tempList.length} contactos)`;
            const body = document.getElementById('tempListBody');

            if (tempList.length === 0) {
                body.innerHTML = '<tr><td colspan="6" class="text-center text-muted">La lista temporal está vacía</td></tr>';
                return;
            }

            body.innerHTML = tempList.map(c => `
            <tr>
                <td><button class="btn btn-sm btn-outline-danger" onclick="removeFromTempList(${c.id})"><i class="bx bx-trash"></i></button></td>
                <td>${c.nombres_y_apellidos_completos || ''}</td>
                <td>${c.empresa || ''}</td>
                <td>${c.cargo || ''}</td>
                <td>${c.correo_corporativo || c.correo_electronico || ''}</td>
                <td>${c.celular || ''}</td>
            </tr>
        `).join('');
        }

        // ---------- Carga de CSV inteligente ----------
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const file = csvFile.files[0];
            if (!file) {
                showMessage('Por favor, seleccione un archivo CSV.', 'error');
                return;
            }

            const submitBtn = uploadForm.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Cargando, no cierres esta pestaña...';

            const formData = new FormData();
            formData.append('csvFile', file);

            fetch(API.csv, {
                    method: 'POST',
                    body: formData
                })
                .then(res => {
                    if (!res.ok) return res.json().then(err => {
                        throw new Error(err.message || 'Error al cargar el archivo');
                    });
                    return res.json();
                })
                .then(data => {
                    showMessage(data.message, 'success');
                    csvFile.value = '';
                })
                .catch(err => showMessage('Error al cargar el archivo: ' + err.message, 'error'))
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                });
        });

        // ---------- Filtros rápidos: país y empresa ----------
        fetch(API.paises)
            .then(res => res.json())
            .then(data => {
                data.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p;
                    opt.textContent = p;
                    filtroPais.appendChild(opt);
                });
            })
            .catch(() => {});

        $(document).ready(function() {
            $('#filtroEmpresa').select2({
                placeholder: 'Buscar empresa...',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: API.empresasBuscar,
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(e => ({
                                id: e,
                                text: e
                            }))
                        };
                    }
                }
            });

            $('#filtroEmpresa').on('change', function() {
                ejecutarBusqueda(searchInput.value.trim());
            });
        });

        filtroPais.addEventListener('change', function() {
            ejecutarBusqueda(searchInput.value.trim());
        });

        limpiarFiltrosButton.addEventListener('click', function() {
            searchInput.value = '';
            filtroPais.value = '';
            $('#filtroEmpresa').val(null).trigger('change');
        });

        // ---------- Búsqueda en tiempo real (texto + filtros) ----------
        function ejecutarBusqueda(term) {
            const pais = filtroPais.value;
            const empresa = $('#filtroEmpresa').val() || '';

            if (!term && !pais && !empresa) {
                resultsDiv.innerHTML = '<div class="ctc-empty-state"><i class="bx bx-search-alt"></i>Empieza a escribir o usa los filtros para buscar.</div>';
                globalResults = [];
                searchSpinner.style.display = 'none';
                return;
            }

            if (searchAbortController) {
                searchAbortController.abort();
            }
            searchAbortController = new AbortController();

            const myRequestId = ++searchRequestId;
            searchSpinner.style.display = 'inline-block';

            const params = new URLSearchParams();
            if (term) params.set('term', term);
            if (pais) params.set('pais', pais);
            if (empresa) params.set('empresa', empresa);

            fetch(`${API.buscar}?${params.toString()}`, {
                    signal: searchAbortController.signal
                })
                .then(res => {
                    if (!res.ok) throw new Error('Error en la búsqueda');
                    return res.json();
                })
                .then(data => {
                    if (myRequestId !== searchRequestId) return;
                    globalResults = data;
                    displayResults(data);
                })
                .catch(err => {
                    if (err.name === 'AbortError') return;
                    showMessage('Error en la búsqueda: ' + err.message, 'error');
                })
                .finally(() => {
                    if (myRequestId === searchRequestId) {
                        searchSpinner.style.display = 'none';
                    }
                });
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const term = searchInput.value.trim();
            searchTimeout = setTimeout(() => ejecutarBusqueda(term), 250);
        });

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearTimeout(searchTimeout);
            ejecutarBusqueda(searchInput.value.trim());
        });

        // ---------- Renderizado de resultados ----------
        function displayResults(results) {
            resultsDiv.innerHTML = '';

            if (results.length === 0) {
                resultsDiv.innerHTML = '<div class="ctc-empty-state"><i class="bx bx-search-alt"></i>No se encontraron resultados.</div>';
                return;
            }

            const tempList = getTempList();
            const tempListIds = tempList.map(c => c.id.toString());

            const fields = [
                'nombres_y_apellidos_completos', 'empresa', 'cargo', 'correo_corporativo',
                'correo_electronico', 'celular', 'telefono', 'linkedin', 'pais', 'nota_origen',
                'status_correo_corporativo'
            ];
            const headers = [
                'Nombres y Apellidos', 'Empresa', 'Cargo', 'Correo Corporativo',
                'Correo Personal', 'Celular', 'Teléfono', 'LinkedIn', 'País', 'Nota', 'Status Correo'
            ];

            let thead = `<tr><th><input type="checkbox" id="selectAll" class="form-check-input"></th><th>Acciones</th>${headers.map(h => `<th>${h}</th>`).join('')}</tr>`;

            let tbody = results.map(r => {
                const isInTemp = tempListIds.includes(r.id.toString());
                return `
                <tr class="${isInTemp ? 'table-info' : ''}">
                    <td>
                        <input type="checkbox" class="row-checkbox form-check-input" data-contact-id="${r.id}" ${isInTemp ? 'disabled title="Ya está en la lista temporal"' : ''}>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${r.id}">Editar</button>
                        ${isInTemp ? `<button class="btn btn-sm btn-outline-danger ms-1 remove-temp-btn" data-id="${r.id}">Quitar</button>` : ''}
                    </td>
                    ${fields.map(f => `<td title="${(r[f] || '').toString().replace(/"/g, '&quot;')}">${r[f] || ''}</td>`).join('')}
                </tr>
            `;
            }).join('');

            resultsDiv.innerHTML = `
            <p class="text-muted small mb-2">${results.length} resultado(s)${results.length === 500 ? ' (mostrando los primeros 500, afina tu búsqueda para ver menos)' : ''}</p>
            <div class="table-responsive" style="max-height:600px;">
                <table class="table table-striped table-hover align-middle ctc-table">
                    <thead class="table-light">${thead}</thead>
                    <tbody>${tbody}</tbody>
                </table>
            </div>
        `;

            document.getElementById('selectAll').addEventListener('change', function() {
                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
                updateDeleteButton();
            });

            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', updateDeleteButton);
            });

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const contact = globalResults.find(c => c.id.toString() === this.dataset.id);
                    if (contact) openEditModal(contact);
                });
            });

            document.querySelectorAll('.remove-temp-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    removeFromTempList(parseInt(this.dataset.id));
                });
            });
        }

        function updateDeleteButton() {
            deleteButton.disabled = document.querySelectorAll('.row-checkbox:checked').length === 0;
        }

        deleteButton.addEventListener('click', function() {
            const selected = document.querySelectorAll('.row-checkbox:checked');
            const ids = Array.from(selected).map(cb => cb.dataset.contactId);

            Swal.fire({
                title: `¿Eliminar ${ids.length} contacto(s)?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(API.eliminar, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ids
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        globalResults = globalResults.filter(c => !ids.includes(c.id.toString()));
                        displayResults(globalResults);
                        showMessage(data.message, 'success');
                    })
                    .catch(err => showMessage('Error al eliminar contactos: ' + err.message, 'error'));
            });
        });

        function openEditModal(contact) {
            document.getElementById('edit_id').value = contact.id;
            document.getElementById('edit_nombres').value = contact.nombres_y_apellidos_completos || '';
            document.getElementById('edit_empresa').value = contact.empresa || '';
            document.getElementById('edit_cargo').value = contact.cargo || '';
            document.getElementById('edit_correo_corporativo').value = contact.correo_corporativo || '';
            document.getElementById('edit_correo_electronico').value = contact.correo_electronico || '';
            document.getElementById('edit_celular').value = contact.celular || '';
            document.getElementById('edit_telefono').value = contact.telefono || '';
            document.getElementById('edit_linkedin').value = contact.linkedin || '';
            document.getElementById('edit_pais').value = contact.pais || '';
            document.getElementById('edit_status_correo').value = contact.status_correo_corporativo || '';
            document.getElementById('edit_nota').value = contact.nota_origen || '';
            editModal.show();
        }

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const formData = new FormData(editForm);
            const updates = Object.fromEntries(formData.entries());
            delete updates.id;

            fetch(API.actualizar(id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(updates)
                })
                .then(res => {
                    if (!res.ok) return res.json().then(err => {
                        throw new Error(err.message || 'Error al actualizar');
                    });
                    return res.json();
                })
                .then(updatedContact => {
                    const index = globalResults.findIndex(c => c.id.toString() === id.toString());
                    if (index !== -1) {
                        globalResults[index] = updatedContact;
                        displayResults(globalResults);
                    }
                    const explorerIndex = explorerState.data.findIndex(c => c.id.toString() === id.toString());
                    if (explorerIndex !== -1) {
                        explorerState.data[explorerIndex] = updatedContact;
                        renderExplorerTable();
                    }
                    editModal.hide();
                    showMessage('Contacto actualizado exitosamente', 'success');
                })
                .catch(err => showMessage('Error al actualizar contacto: ' + err.message, 'error'));
        });

        function getDataToDownload() {
            const selected = document.querySelectorAll('.row-checkbox:checked');
            if (selected.length > 0) {
                const ids = Array.from(selected).map(cb => cb.dataset.contactId);
                return globalResults.filter(r => ids.includes(r.id.toString()));
            }
            return getTempList();
        }

        function downloadFile(content, fileName, mimeType) {
            const blob = new Blob([content], {
                type: mimeType
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = fileName;
            link.click();
        }

        downloadCSVButton.addEventListener('click', function() {
            const data = getDataToDownload();
            if (data.length === 0) {
                showMessage('No hay contactos seleccionados ni en la lista temporal para descargar', 'error');
                return;
            }

            const fields = ['nombres_y_apellidos_completos', 'empresa', 'correo_electronico', 'celular', 'telefono', 'nota_origen', 'status_correo_corporativo'];
            const headers = ['Nombres y Apellidos', 'Empresa', 'Correo Electrónico', 'Celular', 'Teléfono', 'Nota de Origen', 'Status Correo Corp.'];

            let csvContent = headers.join(',') + '\n';
            data.forEach(row => {
                csvContent += fields.map(f => {
                    let value = (row[f] || '').toString().replace(/"/g, '""');
                    return `"${value}"`;
                }).join(',') + '\n';
            });

            downloadFile(csvContent, 'resultados_busqueda.csv', 'text/csv;charset=utf-8;');
        });

        downloadExcelButton.addEventListener('click', function() {
            const data = getDataToDownload();
            if (data.length === 0) {
                showMessage('No hay contactos seleccionados ni en la lista temporal para descargar', 'error');
                return;
            }

            const fields = ['nombres_y_apellidos_completos', 'empresa', 'correo_electronico', 'celular', 'telefono', 'nota_origen', 'status_correo_corporativo'];
            const headers = ['Nombres y Apellidos', 'Empresa', 'Correo Electrónico', 'Celular', 'Teléfono', 'Nota de Origen', 'Status Correo Corp.'];

            const ws = XLSX.utils.json_to_sheet(data, {
                header: fields
            });
            XLSX.utils.sheet_add_aoa(ws, [headers], {
                origin: 'A1'
            });
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Resultados');
            XLSX.writeFile(wb, 'resultados_busqueda.xlsx');
        });

        addToTempListButton.addEventListener('click', addSelectedToTempList);
        clearTempListButton.addEventListener('click', clearTempList);
        viewTempListButton.addEventListener('click', function() {
            renderTempListModal();
            tempListModal.show();
        });

        // ---------- Duplicados ----------
        buscarDuplicadosButton.addEventListener('click', function() {
            duplicadosModal.show();
            cargarDuplicados();
        });

        function cargarDuplicados() {
            const body = document.getElementById('duplicadosBody');
            const resumen = document.getElementById('duplicadosResumen');
            body.innerHTML = '<div class="ctc-empty-state"><i class="bx bx-loader-alt bx-spin"></i>Buscando duplicados...</div>';
            resumen.textContent = '';

            fetch(API.duplicados)
                .then(res => res.json())
                .then(data => {
                    if (!data.grupos || data.grupos.length === 0) {
                        body.innerHTML = '<div class="ctc-empty-state"><i class="bx bx-check-circle"></i>No se encontraron contactos duplicados.</div>';
                        return;
                    }

                    resumen.textContent = `${data.totalGrupos} grupo(s) de duplicados${data.truncado ? ' (mostrando los primeros 200)' : ''}`;

                    body.innerHTML = data.grupos.map((grupo, gi) => `
                    <div class="card ctc-card mb-3">
                        <div class="card-header py-2">
                            <strong>${grupo.criterio}:</strong> ${grupo.valor}
                            <span class="badge bg-warning text-dark ms-2">${grupo.contactos.length} coincidencias</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mantener</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Empresa</th>
                                        <th>Correo</th>
                                        <th>Registrado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${grupo.contactos.map((c, ci) => `
                                        <tr>
                                            <td>
                                                <input type="radio" name="mantener-${gi}" value="${c.id}" class="form-check-input" ${ci === 0 ? 'checked' : ''}>
                                            </td>
                                            <td>${c.nombres_y_apellidos_completos || ''}</td>
                                            <td>${c.empresa || ''}</td>
                                            <td>${c.correo_corporativo || c.correo_electronico || ''}</td>
                                            <td>${c.created_at || ''}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `).join('');
                })
                .catch(err => {
                    body.innerHTML = `<div class="ctc-empty-state text-danger"><i class="bx bx-error-circle"></i>Error al buscar duplicados: ${err.message}</div>`;
                });
        }

        document.getElementById('eliminarDuplicadosButton').addEventListener('click', function() {
            const grupos = document.querySelectorAll('#duplicadosBody .card');
            if (grupos.length === 0) {
                showMessage('No hay duplicados cargados', 'error');
                return;
            }

            const idsAEliminar = [];

            grupos.forEach((grupoEl, gi) => {
                const radios = grupoEl.querySelectorAll(`input[name="mantener-${gi}"]`);
                const mantenerId = Array.from(radios).find(r => r.checked)?.value;
                const filas = grupoEl.querySelectorAll('tbody tr');
                filas.forEach(fila => {
                    const radio = fila.querySelector('input[type="radio"]');
                    if (radio.value !== mantenerId) {
                        idsAEliminar.push(radio.value);
                    }
                });
            });

            const idsUnicos = [...new Set(idsAEliminar)];

            if (idsUnicos.length === 0) {
                showMessage('No hay contactos para eliminar', 'error');
                return;
            }

            Swal.fire({
                title: `¿Eliminar ${idsUnicos.length} contacto(s) duplicado(s)?`,
                text: 'Se conservará el contacto marcado como "Mantener" en cada grupo.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar duplicados',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(API.eliminar, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ids: idsUnicos
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.message, 'success');
                        duplicadosModal.hide();
                        if (globalResults.length > 0) {
                            globalResults = globalResults.filter(c => !idsUnicos.includes(c.id.toString()));
                            displayResults(globalResults);
                        }
                    })
                    .catch(err => showMessage('Error al eliminar duplicados: ' + err.message, 'error'));
            });
        });

        updateTempListCounter();

        // ======================================================
        // ---------- Columnas disponibles (mapa dbField->Label) ----------
        // ======================================================
        let allColumnsMap = {}; // dbField => Label
        const DEFAULT_EXPLORER_COLUMNS = [
            'nombres_y_apellidos_completos', 'empresa', 'cargo', 'correo_corporativo',
            'correo_electronico', 'celular', 'telefono', 'pais', 'linkedin', 'nota_origen',
        ];

        function getVisibleColumns() {
            try {
                const saved = JSON.parse(localStorage.getItem('explorerColumnas') || 'null');
                if (Array.isArray(saved) && saved.length > 0) return saved;
            } catch (e) {}
            return DEFAULT_EXPLORER_COLUMNS.slice();
        }

        function setVisibleColumns(cols) {
            localStorage.setItem('explorerColumnas', JSON.stringify(cols));
        }

        function cargarColumnas() {
            return fetch(API.columnas)
                .then(res => res.json())
                .then(data => {
                    allColumnsMap = data;
                    renderColumnPicker();
                })
                .catch(() => {});
        }

        function renderColumnPicker() {
            const menu = document.getElementById('explorerColumnasMenu');
            const visible = getVisibleColumns();
            menu.innerHTML = Object.entries(allColumnsMap).map(([field, label]) => `
                <div class="form-check">
                    <input class="form-check-input columna-toggle" type="checkbox" value="${field}" id="col_${field}" ${visible.includes(field) ? 'checked' : ''}>
                    <label class="form-check-label small" for="col_${field}">${label}</label>
                </div>
            `).join('');

            menu.querySelectorAll('.columna-toggle').forEach(cb => {
                cb.addEventListener('change', function() {
                    let cols = getVisibleColumns();
                    if (this.checked) {
                        if (!cols.includes(this.value)) cols.push(this.value);
                    } else {
                        cols = cols.filter(c => c !== this.value);
                    }
                    setVisibleColumns(cols);
                    renderExplorerTable();
                });
            });
        }

        // ======================================================
        // ---------- Explorador de Datos (paginación real) ----------
        // ======================================================
        const explorerModalEl = document.getElementById('explorerModal');
        const explorerModal = new bootstrap.Modal(explorerModalEl);

        const explorerState = {
            page: 1,
            perPage: 50,
            filtros: { term: '', pais: '', empresa: '', reglas: [] },
            data: [],
            total: 0,
            totalPages: 0,
            selectedIds: new Set(),
        };
        // Guarda los datos completos de cada contacto que se haya visto/seleccionado en el explorador.
        const explorerContactosCache = new Map();

        document.getElementById('openExplorerButton').addEventListener('click', function() {
            explorerModal.show();
            if (Object.keys(allColumnsMap).length === 0) {
                cargarColumnas().then(cargarExplorerPaises).then(() => cargarExplorerDatos());
            } else {
                cargarExplorerDatos();
            }
        });

        function cargarExplorerPaises() {
            const select = document.getElementById('explorerPais');
            if (select.dataset.loaded) return;
            return fetch(API.paises)
                .then(res => res.json())
                .then(data => {
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p;
                        opt.textContent = p;
                        select.appendChild(opt);
                    });
                    select.dataset.loaded = '1';
                })
                .catch(() => {});
        }

        function construirQueryFiltros(filtros) {
            const params = new URLSearchParams();
            if (filtros.term) params.set('term', filtros.term);
            if (filtros.pais) params.set('pais', filtros.pais);
            if (filtros.empresa) params.set('empresa', filtros.empresa);
            if (filtros.reglas && filtros.reglas.length > 0) params.set('reglas', JSON.stringify(filtros.reglas));
            return params;
        }

        function cargarExplorerDatos() {
            const tbody = document.getElementById('explorerTbody');
            tbody.innerHTML = '<tr><td class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Cargando...</td></tr>';

            const params = construirQueryFiltros(explorerState.filtros);
            params.set('page', explorerState.page);
            params.set('perPage', explorerState.perPage);

            fetch(`${API.listar}?${params.toString()}`)
                .then(res => res.json())
                .then(data => {
                    explorerState.data = data.data;
                    explorerState.total = data.total;
                    explorerState.totalPages = data.totalPages;
                    explorerState.page = data.page;
                    data.data.forEach(c => explorerContactosCache.set(c.id, c));
                    renderExplorerTable();
                    renderExplorerPagination();
                    document.getElementById('explorerTotalLabel').textContent =
                        `${data.total.toLocaleString('es-PE')} coinciden`;
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td class="text-center text-danger py-4">Error al cargar: ${err.message}</td></tr>`;
                });
        }

        function renderExplorerTable() {
            const cols = getVisibleColumns();
            const thead = document.getElementById('explorerThead');
            const tbody = document.getElementById('explorerTbody');

            thead.innerHTML = `<tr>
                <th><input type="checkbox" id="explorerSelectAll" class="form-check-input"></th>
                <th>Acciones</th>
                ${cols.map(c => `<th>${allColumnsMap[c] || c}</th>`).join('')}
            </tr>`;

            if (explorerState.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="' + (cols.length + 2) + '" class="text-center text-muted py-4">Sin resultados.</td></tr>';
            } else {
                tbody.innerHTML = explorerState.data.map(r => `
                    <tr>
                        <td><input type="checkbox" class="form-check-input explorer-row-checkbox" data-id="${r.id}" ${explorerState.selectedIds.has(r.id) ? 'checked' : ''}></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary explorer-edit-btn" data-id="${r.id}">Editar</button>
                        </td>
                        ${cols.map(c => `<td title="${(r[c] || '').toString().replace(/"/g, '&quot;')}">${r[c] || ''}</td>`).join('')}
                    </tr>
                `).join('');
            }

            const selectAll = document.getElementById('explorerSelectAll');
            const idsEnPagina = explorerState.data.map(r => r.id);
            selectAll.checked = idsEnPagina.length > 0 && idsEnPagina.every(id => explorerState.selectedIds.has(id));
            selectAll.addEventListener('change', function() {
                idsEnPagina.forEach(id => {
                    if (this.checked) explorerState.selectedIds.add(id);
                    else explorerState.selectedIds.delete(id);
                });
                renderExplorerTable();
                updateExplorerSeleccionLabel();
            });

            document.querySelectorAll('.explorer-row-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const id = parseInt(this.dataset.id);
                    if (this.checked) explorerState.selectedIds.add(id);
                    else explorerState.selectedIds.delete(id);
                    updateExplorerSeleccionLabel();
                });
            });

            document.querySelectorAll('.explorer-edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const contact = explorerState.data.find(c => c.id.toString() === this.dataset.id);
                    if (contact) openEditModal(contact);
                });
            });

            updateExplorerSeleccionLabel();
        }

        function updateExplorerSeleccionLabel() {
            const n = explorerState.selectedIds.size;
            document.getElementById('explorerSeleccionLabel').textContent = `${n} seleccionado(s) (en todas las páginas)`;
            document.getElementById('explorerEliminarSeleccionadosButton').disabled = n === 0;
        }

        function renderExplorerPagination() {
            const pag = document.getElementById('explorerPagination');
            const info = document.getElementById('explorerPageInfo');
            const { page, totalPages, total, perPage } = explorerState;

            info.textContent = total === 0
                ? 'Sin resultados'
                : `Página ${page} de ${totalPages} — mostrando ${Math.min(perPage, total - (page - 1) * perPage)} de ${total.toLocaleString('es-PE')}`;

            if (totalPages <= 1) {
                pag.innerHTML = '';
                return;
            }

            const botones = [];
            const addBtn = (label, target, disabled = false, active = false) => {
                botones.push(`<li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
                    <button type="button" class="page-link explorer-page-btn" data-page="${target}">${label}</button>
                </li>`);
            };

            addBtn('«', 1, page === 1);
            addBtn('‹', page - 1, page === 1);

            let start = Math.max(1, page - 2);
            let end = Math.min(totalPages, page + 2);
            for (let p = start; p <= end; p++) {
                addBtn(p, p, false, p === page);
            }

            addBtn('›', page + 1, page === totalPages);
            addBtn('»', totalPages, page === totalPages);

            pag.innerHTML = botones.join('');
            pag.querySelectorAll('.explorer-page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = parseInt(this.dataset.page);
                    if (target >= 1 && target <= explorerState.totalPages && target !== explorerState.page) {
                        explorerState.page = target;
                        cargarExplorerDatos();
                    }
                });
            });
        }

        document.getElementById('explorerPerPage').addEventListener('change', function() {
            explorerState.perPage = parseInt(this.value);
            explorerState.page = 1;
            cargarExplorerDatos();
        });

        document.getElementById('explorerBuscarButton').addEventListener('click', function() {
            explorerState.filtros.term = document.getElementById('explorerTerm').value.trim();
            explorerState.filtros.pais = document.getElementById('explorerPais').value;
            explorerState.filtros.empresa = document.getElementById('explorerEmpresa').value.trim();
            explorerState.page = 1;
            cargarExplorerDatos();
        });

        document.getElementById('explorerLimpiarButton').addEventListener('click', function() {
            document.getElementById('explorerTerm').value = '';
            document.getElementById('explorerPais').value = '';
            document.getElementById('explorerEmpresa').value = '';
            explorerState.filtros = { term: '', pais: '', empresa: '', reglas: [] };
            explorerState.page = 1;
            cargarExplorerDatos();
        });

        document.getElementById('explorerLimpiarSeleccionButton').addEventListener('click', function() {
            explorerState.selectedIds.clear();
            renderExplorerTable();
        });

        document.getElementById('explorerAgregarListaButton').addEventListener('click', function() {
            if (explorerState.selectedIds.size === 0) {
                showMessage('Selecciona al menos un contacto', 'error');
                return;
            }
            const tempList = getTempList();
            const seleccionados = explorerState.data.filter(c => explorerState.selectedIds.has(c.id) && !tempList.some(t => t.id === c.id));
            updateTempList([...tempList, ...seleccionados]);
            showMessage(`${seleccionados.length} contacto(s) agregados a la lista temporal (solo los de la página actual)`, 'success');
        });

        document.getElementById('explorerEliminarSeleccionadosButton').addEventListener('click', function() {
            const ids = Array.from(explorerState.selectedIds);
            if (ids.length === 0) return;

            Swal.fire({
                title: `¿Eliminar ${ids.length} contacto(s) seleccionado(s)?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(API.eliminar, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids })
                    })
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.message, 'success');
                        explorerState.selectedIds.clear();
                        cargarExplorerDatos();
                        cargarTotalContactos();
                    })
                    .catch(err => showMessage('Error al eliminar: ' + err.message, 'error'));
            });
        });

        // ======================================================
        // ---------- Agregar Contacto ----------
        // ======================================================
        const crearModalEl = document.getElementById('crearModal');
        const crearModal = new bootstrap.Modal(crearModalEl);
        const crearForm = document.getElementById('crearForm');

        document.getElementById('openCrearButton').addEventListener('click', function() {
            crearForm.reset();
            crearModal.show();
        });

        crearForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(crearForm);
            const data = Object.fromEntries(formData.entries());

            const submitBtn = crearForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            fetch(API.crear, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(res => {
                    if (!res.ok) return res.json().then(err => { throw new Error(err.message || 'Error al crear el contacto'); });
                    return res.json();
                })
                .then(() => {
                    crearModal.hide();
                    showMessage('Contacto creado exitosamente', 'success');
                    cargarTotalContactos();
                    if (explorerModalEl.classList.contains('show')) cargarExplorerDatos();
                })
                .catch(err => showMessage('Error al crear contacto: ' + err.message, 'error'))
                .finally(() => { submitBtn.disabled = false; });
        });

        // ======================================================
        // ---------- Segmentar y Exportar ----------
        // ======================================================
        const segmentarModalEl = document.getElementById('segmentarModal');
        const segmentarModal = new bootstrap.Modal(segmentarModalEl);
        const reglasContainer = document.getElementById('segmentarReglas');

        const OPERADORES = [
            { value: 'contiene', label: 'contiene' },
            { value: 'no_contiene', label: 'no contiene' },
            { value: 'igual', label: 'es igual a' },
            { value: 'diferente', label: 'es diferente de' },
            { value: 'empieza', label: 'empieza con' },
            { value: 'termina', label: 'termina con' },
            { value: 'vacio', label: 'está vacío' },
            { value: 'no_vacio', label: 'no está vacío' },
        ];

        let reglaContador = 0;

        function agregarFilaRegla() {
            reglaContador++;
            const id = reglaContador;
            const div = document.createElement('div');
            div.className = 'row g-2 mb-2 align-items-center regla-fila';
            div.dataset.reglaId = id;
            div.innerHTML = `
                <div class="col-md-4">
                    <select class="form-select form-select-sm regla-campo">
                        ${Object.entries(allColumnsMap).map(([field, label]) => `<option value="${field}">${label}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm regla-operador">
                        ${OPERADORES.map(o => `<option value="${o.value}">${o.label}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm regla-valor" placeholder="valor...">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-outline-danger regla-quitar"><i class="bx bx-x"></i></button>
                </div>
            `;
            reglasContainer.appendChild(div);

            const operadorSelect = div.querySelector('.regla-operador');
            const valorInput = div.querySelector('.regla-valor');
            operadorSelect.addEventListener('change', function() {
                const esVacio = this.value === 'vacio' || this.value === 'no_vacio';
                valorInput.disabled = esVacio;
                valorInput.value = esVacio ? '' : valorInput.value;
            });

            div.querySelector('.regla-quitar').addEventListener('click', function() {
                div.remove();
            });
        }

        document.getElementById('segmentarAgregarReglaButton').addEventListener('click', agregarFilaRegla);

        document.getElementById('openSegmentarButton').addEventListener('click', function() {
            const abrir = () => {
                if (reglasContainer.children.length === 0) agregarFilaRegla();
                document.getElementById('segmentarPreviewResultado').textContent = '';
                segmentarModal.show();
            };
            if (Object.keys(allColumnsMap).length === 0) {
                cargarColumnas().then(abrir);
            } else {
                abrir();
            }
        });

        function leerReglasSegmentacion() {
            return Array.from(reglasContainer.querySelectorAll('.regla-fila')).map(fila => ({
                campo: fila.querySelector('.regla-campo').value,
                operador: fila.querySelector('.regla-operador').value,
                valor: fila.querySelector('.regla-valor').value.trim(),
            }));
        }

        document.getElementById('segmentarPreviewButton').addEventListener('click', function() {
            const reglas = leerReglasSegmentacion();
            const resultado = document.getElementById('segmentarPreviewResultado');
            resultado.textContent = 'Contando...';

            const params = new URLSearchParams();
            if (reglas.length > 0) params.set('reglas', JSON.stringify(reglas));

            fetch(`${API.contarFiltro}?${params.toString()}`)
                .then(res => res.json())
                .then(data => {
                    resultado.textContent = `${data.total.toLocaleString('es-PE')} contacto(s) coinciden`;
                })
                .catch(err => {
                    resultado.textContent = '';
                    showMessage('Error al contar: ' + err.message, 'error');
                });
        });

        document.getElementById('segmentarVerExplorerButton').addEventListener('click', function() {
            const reglas = leerReglasSegmentacion();
            explorerState.filtros = { term: '', pais: '', empresa: '', reglas };
            explorerState.page = 1;
            document.getElementById('explorerTerm').value = '';
            document.getElementById('explorerPais').value = '';
            document.getElementById('explorerEmpresa').value = '';
            segmentarModal.hide();
            explorerModal.show();
            cargarExplorerPaises().then(() => cargarExplorerDatos());
        });

        document.getElementById('segmentarExportarButton').addEventListener('click', function() {
            const reglas = leerReglasSegmentacion();
            const params = new URLSearchParams();
            if (reglas.length > 0) params.set('reglas', JSON.stringify(reglas));
            window.location.href = `${API.exportarCsv}?${params.toString()}`;
        });

        document.getElementById('segmentarEliminarButton').addEventListener('click', function() {
            const reglas = leerReglasSegmentacion();
            if (reglas.length === 0) {
                showMessage('Agrega al menos una regla antes de eliminar en bloque', 'error');
                return;
            }

            Swal.fire({
                title: 'Eliminar contactos del segmento',
                html: 'Esta acción no se puede deshacer.<br>Escribe <b>ELIMINAR</b> para confirmar:',
                input: 'text',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(API.eliminarPorFiltro, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ filtros: { reglas }, confirmacion: result.value })
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(err => { throw new Error(err.message || 'Error al eliminar'); });
                        return res.json();
                    })
                    .then(data => {
                        showMessage(data.message, 'success');
                        document.getElementById('segmentarPreviewResultado').textContent = '';
                        cargarTotalContactos();
                    })
                    .catch(err => showMessage('Error: ' + err.message, 'error'));
            });
        });

        // ---------- Total de contactos en la BD (informativo) ----------
        function cargarTotalContactos() {
            fetch(base_url + 'contactos/total')
                .then(res => res.json())
                .then(data => {
                    const label = document.getElementById('totalContactosLabel');
                    if (label) {
                        label.innerHTML = `<i class="bx bx-database"></i> ${data.total.toLocaleString('es-PE')} en total`;
                    }
                })
                .catch(() => {
                    const label = document.getElementById('totalContactosLabel');
                    if (label) label.innerHTML = '';
                });
        }

        cargarTotalContactos();

    })();
</script>
<?= $this->endSection() ?>