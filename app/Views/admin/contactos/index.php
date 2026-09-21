<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

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

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Cargar Archivo CSV</h5>
        <form id="uploadForm" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Seleccionar archivo (separado por ";" o ",")</label>
                <input type="file" id="csvFile" accept=".csv" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Evento <span class="text-muted small">(opcional)</span></label>
                <select id="csvEvento" class="form-select">
                    <option value="">Sin evento / cargar suelto</option>
                    <option value="__nuevo__">+ Crear nuevo evento...</option>
                </select>
                <input type="text" id="csvEventoNuevoNombre" class="form-control mt-2" placeholder="Nombre del nuevo evento" style="display:none;">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bx bx-upload me-1"></i> Cargar CSV
                </button>
            </div>
            <div class="col-12">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i>
                    Si asocias esta carga a un evento, todos los contactos nuevos (y los que se actualicen por duplicado) quedarán etiquetados con ese evento. Podrás buscarlos y filtrarlos por evento más adelante, y ver esta carga en el <strong>Historial</strong>.
                </small>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h5 class="card-title mb-0">
                Búsqueda de Registros
                <span class="text-muted small fw-normal ms-2" id="totalContactosLabel">(cargando total...)</span>
            </h5>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="text-muted small" id="tempListCounter">Lista temporal: 0 contactos</span>
                <button id="addToTempListButton" class="btn btn-sm btn-info text-white">
                    <i class="bx bx-list-ul me-1"></i> Agregar a lista
                </button>
                <button id="viewTempListButton" class="btn btn-sm btn-primary">
                    <i class="bx bx-show me-1"></i> Ver lista
                </button>
                <button id="downloadCSVButton" class="btn btn-sm btn-success">
                    <i class="bx bx-file me-1"></i> CSV
                </button>
                <button id="downloadExcelButton" class="btn btn-sm btn-success">
                    <i class="bx bxs-file-export me-1"></i> Excel
                </button>
                <button id="clearTempListButton" class="btn btn-sm btn-danger">
                    <i class="bx bx-trash me-1"></i> Limpiar lista
                </button>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <button id="openExplorerButton" class="btn btn-sm btn-dark">
                <i class="bx bx-table me-1"></i> Ver toda la data
            </button>
            <button id="openHistorialButton" class="btn btn-sm btn-secondary">
                <i class="bx bx-history me-1"></i> Historial
            </button>
            <button id="openCrearButton" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-user-plus me-1"></i> Agregar contacto
            </button>
            <button id="openSegmentarButton" class="btn btn-sm" style="background:#6f42c1;color:#fff;">
                <i class="bx bx-filter-alt me-1"></i> Segmentar y exportar
            </button>
            <button id="openCorreosButton" class="btn btn-sm btn-danger">
                <i class="bx bx-envelope me-1"></i> Enviar Correos
            </button>
        </div>

        <!-- Filtros rápidos -->
        <div class="row g-2 mb-2">
            <div class="col-md-3">
                <select id="filtroPais" class="form-select">
                    <option value="">Todos los países</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filtroEvento" class="form-select">
                    <option value="">Todos los eventos</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filtroEmpresa" class="form-control" style="width:100%"></select>
            </div>
            <div class="col-md-1">
                <button type="button" id="limpiarFiltrosButton" class="btn btn-outline-secondary w-100">
                    <i class="bx bx-x me-1"></i> Limpiar filtros
                </button>
            </div>
            <div class="col-md-2">
                <button type="button" id="buscarDuplicadosButton" class="btn btn-warning w-100">
                    <i class="bx bx-copy-alt me-1"></i> Buscar duplicados
                </button>
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
            <p class="text-muted text-center py-4">Empieza a escribir o usa los filtros para buscar.</p>
        </div>
    </div>
</div>

<!-- Modal Editar Contacto -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-height: 90vh; margin-top: 5vh; margin-bottom: 5vh;">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title">Editar Contacto</h5>
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
                <h5 class="modal-title" id="tempListModalTitle">Lista Temporal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
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
                <h5 class="modal-title">Contactos duplicados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y:auto; flex-grow:1;" id="duplicadosBody">
                <p class="text-muted text-center py-4">Buscando duplicados...</p>
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

<!-- Modal Historial de cargas / Eventos -->
<div class="modal fade" id="historialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-height:90vh; margin-top:5vh; margin-bottom:5vh;">
        <div class="modal-content" style="max-height:90vh; display:flex; flex-direction:column;">
            <div class="modal-header" style="flex-shrink:0;">
                <h5 class="modal-title">Historial de cargas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y:auto; flex-grow:1;">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="historialTabCargasBtn" data-bs-toggle="tab" data-bs-target="#historialTabCargas" type="button" role="tab">
                            <i class="bx bx-upload me-1"></i> Cargas realizadas
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="historialTabEventosBtn" data-bs-toggle="tab" data-bs-target="#historialTabEventos" type="button" role="tab">
                            <i class="bx bx-calendar-event me-1"></i> Eventos
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab: cargas / importaciones -->
                    <div class="tab-pane fade show active" id="historialTabCargas" role="tabpanel">
                        <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
                            <div>
                                <label class="form-label small mb-1">Filtrar por evento</label>
                                <select id="historialFiltroEvento" class="form-select form-select-sm" style="width:220px">
                                    <option value="">Todos los eventos</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Archivo</th>
                                        <th>Evento</th>
                                        <th>Total filas</th>
                                        <th>Nuevos</th>
                                        <th>Actualizados</th>
                                        <th>Usuario</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="historialCargasTbody">
                                    <tr><td colspan="8" class="text-center text-muted py-4">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <nav class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted small" id="historialCargasPageInfo"></span>
                            <ul class="pagination pagination-sm mb-0" id="historialCargasPagination"></ul>
                        </nav>
                    </div>

                    <!-- Tab: eventos -->
                    <div class="tab-pane fade" id="historialTabEventos" role="tabpanel">
                        <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
                            <div class="flex-grow-1" style="max-width:220px;">
                                <label class="form-label small mb-1">Nombre del evento</label>
                                <input type="text" id="nuevoEventoNombre" class="form-control form-control-sm" placeholder="Ej. Feria Industrial 2026">
                            </div>
                            <div>
                                <label class="form-label small mb-1">Fecha</label>
                                <input type="date" id="nuevoEventoFecha" class="form-control form-control-sm" style="width:160px">
                            </div>
                            <div>
                                <label class="form-label small mb-1">Lugar</label>
                                <input type="text" id="nuevoEventoLugar" class="form-control form-control-sm" style="width:180px">
                            </div>
                            <button type="button" id="crearEventoButton" class="btn btn-sm btn-primary">
                                <i class="bx bx-plus me-1"></i> Crear evento
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Evento</th>
                                        <th>Fecha</th>
                                        <th>Lugar</th>
                                        <th>Contactos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="historialEventosTbody">
                                    <tr><td colspan="5" class="text-center text-muted py-4">Cargando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink:0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
                    Explorador de Datos
                    <span class="text-muted small fw-normal ms-2" id="explorerTotalLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
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
                        <label class="form-label small mb-1">Evento</label>
                        <select id="explorerEvento" class="form-select form-select-sm" style="width:180px">
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
                    <button type="button" id="explorerEnviarCorreoButton" class="btn btn-sm btn-danger" disabled>
                        <i class="bx bx-envelope me-1"></i> Enviar Correo a seleccionados
                    </button>
                    <button type="button" id="explorerLimpiarSeleccionButton" class="btn btn-sm btn-outline-secondary">
                        Deseleccionar todo
                    </button>
                </div>

                <div id="explorerTableWrapper" class="table-responsive">
                    <table class="table table-striped table-hover align-middle table-sm">
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
                <h5 class="modal-title">Segmentar contactos</h5>
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
                <h5 class="modal-title">Agregar Contacto</h5>
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

<!-- Modal Enviar Correos -->
<div class="modal fade" id="correosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-height:90vh; margin-top:5vh; margin-bottom:5vh;">
        <div class="modal-content" style="max-height:90vh; display:flex; flex-direction:column;">
            <div class="modal-header" style="flex-shrink:0;">
                <h5 class="modal-title">Enviar Correos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y:auto; flex-grow:1;">
                <div class="alert alert-info small mb-3" id="correosCuotaInfo">Cargando cupo diario...</div>

                <h6 class="mb-2">1. Plantilla del correo</h6>
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Asunto</label>
                        <input type="text" id="correoAsunto" class="form-control" placeholder="Puedes usar {{nombre}} y {{empresa}}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Imagen / banner (opcional)</label>
                        <input type="file" id="correoImagenInput" accept="image/png,image/jpeg" class="form-control">
                        <div id="correoImagenPreview" class="mt-2"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PDF adjunto (opcional)</label>
                        <input type="file" id="correoPdfInput" accept="application/pdf" class="form-control">
                        <div id="correoPdfPreview" class="mt-2 small text-muted"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Cuerpo del correo</label>
                        <textarea id="correoCuerpo" class="form-control" rows="5" placeholder="Escribe el mensaje. Puedes usar {{nombre}} y {{empresa}}. Se admite HTML básico."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="button" id="correoVistaPreviaButton" class="btn btn-sm btn-outline-dark">
                            <i class="bx bx-show me-1"></i> Vista previa
                        </button>
                        <span class="text-muted small ms-2">Muestra cómo se verá el correo antes de enviarlo (usa el primer contacto seleccionado, o un ejemplo si no hay ninguno).</span>
                    </div>
                    <div class="col-12" id="correoVistaPreviaPanel" style="display:none;">
                        <div class="border rounded" style="background:#f8f9fa;">
                            <div class="border-bottom p-2 small">
                                <div><strong>Para:</strong> <span id="correoVistaPara"></span></div>
                                <div><strong>Asunto:</strong> <span id="correoVistaAsunto"></span></div>
                                <div id="correoVistaAdjuntoWrap" style="display:none;"><strong>Adjunto:</strong> <span id="correoVistaAdjunto"></span></div>
                            </div>
                            <iframe id="correoVistaFrame" style="width:100%; height:350px; border:0; background:#fff;"></iframe>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="mb-2">
                    2. Destinatarios
                    (<span id="correosContadorLabel">0</span> seleccionado(s) de <span id="correosTotalLabel">0</span> contactos disponibles)
                </h6>
                <p class="text-muted small" id="correosOrigenTexto">Esta lista toma los contactos que tengas cargados arriba en "Búsqueda de Registros". Si no hay resultados, primero busca o filtra contactos.</p>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <button type="button" id="correosSeleccionarTodoButton" class="btn btn-sm btn-outline-primary">Seleccionar todo</button>
                    <button type="button" id="correosSeleccionarPendientesButton" class="btn btn-sm btn-outline-warning">Seleccionar pendientes</button>
                    <button type="button" id="correosDeseleccionarButton" class="btn btn-sm btn-outline-secondary">Deseleccionar</button>
                    <div class="form-check form-switch ms-auto">
                        <input class="form-check-input" type="checkbox" id="correosSoloSeleccionadosToggle">
                        <label class="form-check-label small" for="correosSoloSeleccionadosToggle">Mostrar solo seleccionados</label>
                    </div>
                </div>

                <div class="table-responsive" style="max-height:300px;">
                    <table class="table table-sm table-striped align-middle">
                        <thead class="table-light">
                            <tr><th></th><th>Nombre</th><th>Empresa</th><th>Correo</th><th>Estado</th></tr>
                        </thead>
                        <tbody id="correosDestinatariosBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink:0;">
                <span class="text-muted small me-auto" id="correosResultado"></span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="correosEnviarButton" class="btn btn-danger" disabled>
                    <i class="bx bx-send me-1"></i> Enviar Correo
                </button>
            </div>
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
            eventos: base_url + 'contactos/eventos',
            crearEvento: base_url + 'contactos/eventos/crear',
            historial: base_url + 'contactos/historial',
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
        const filtroEvento = document.getElementById('filtroEvento');
        const limpiarFiltrosButton = document.getElementById('limpiarFiltrosButton');
        const buscarDuplicadosButton = document.getElementById('buscarDuplicadosButton');

        const csvEvento = document.getElementById('csvEvento');
        const csvEventoNuevoNombre = document.getElementById('csvEventoNuevoNombre');

        let eventosCache = [];

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
            document.getElementById('tempListModalTitle').textContent = `Lista Temporal (${tempList.length} contactos)`;
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
        csvEvento.addEventListener('change', function() {
            csvEventoNuevoNombre.style.display = this.value === '__nuevo__' ? 'block' : 'none';
        });

        function poblarSelectEventos(select, { incluirTodos = true, incluirNuevo = false, conteo = false } = {}) {
            const valorActual = select.value;
            select.innerHTML = '';

            if (incluirTodos) {
                const optTodos = document.createElement('option');
                optTodos.value = '';
                optTodos.textContent = incluirNuevo ? 'Sin evento / cargar suelto' : 'Todos los eventos';
                select.appendChild(optTodos);
            }

            eventosCache.forEach(ev => {
                const opt = document.createElement('option');
                opt.value = ev.id;
                opt.textContent = conteo ? `${ev.nombre} (${ev.total_contactos})` : ev.nombre;
                select.appendChild(opt);
            });

            if (incluirNuevo) {
                const optNuevo = document.createElement('option');
                optNuevo.value = '__nuevo__';
                optNuevo.textContent = '+ Crear nuevo evento...';
                select.appendChild(optNuevo);
            }

            if ([...select.options].some(o => o.value === valorActual)) {
                select.value = valorActual;
            }
        }

        function cargarEventos() {
            return fetch(API.eventos)
                .then(res => res.json())
                .then(data => {
                    eventosCache = data;
                    poblarSelectEventos(filtroEvento, { incluirTodos: true });
                    poblarSelectEventos(csvEvento, { incluirTodos: true, incluirNuevo: true });
                    const explorerEventoSelect = document.getElementById('explorerEvento');
                    if (explorerEventoSelect) poblarSelectEventos(explorerEventoSelect, { incluirTodos: true });
                    const historialFiltroEventoSelect = document.getElementById('historialFiltroEvento');
                    if (historialFiltroEventoSelect) poblarSelectEventos(historialFiltroEventoSelect, { incluirTodos: true });
                    return data;
                })
                .catch(() => {});
        }

        cargarEventos();

        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const file = csvFile.files[0];
            if (!file) {
                showMessage('Por favor, seleccione un archivo CSV.', 'error');
                return;
            }

            if (csvEvento.value === '__nuevo__' && csvEventoNuevoNombre.value.trim() === '') {
                showMessage('Escribe el nombre del nuevo evento, o selecciona "Sin evento".', 'error');
                return;
            }

            const submitBtn = uploadForm.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Cargando, no cierres esta pestaña...';

            const formData = new FormData();
            formData.append('csvFile', file);

            if (csvEvento.value === '__nuevo__') {
                formData.append('evento_nombre', csvEventoNuevoNombre.value.trim());
            } else if (csvEvento.value) {
                formData.append('evento_id', csvEvento.value);
            }

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
                    csvEvento.value = '';
                    csvEventoNuevoNombre.value = '';
                    csvEventoNuevoNombre.style.display = 'none';
                    cargarEventos();
                })
                .catch(err => showMessage('Error al cargar el archivo: ' + err.message, 'error'))
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                });
        });

        // ======================================================
        // ---------- Historial de cargas / Administración de eventos ----------
        // ======================================================
        const historialModalEl = document.getElementById('historialModal');
        const historialModal = new bootstrap.Modal(historialModalEl);

        const historialState = {
            page: 1,
            perPage: 25,
            eventoId: '',
            totalPages: 0,
        };

        document.getElementById('openHistorialButton').addEventListener('click', function() {
            historialModal.show();
            cargarEventos().then(() => {
                poblarSelectEventos(document.getElementById('historialFiltroEvento'), { incluirTodos: true });
            });
            historialState.page = 1;
            cargarHistorialCargas();
            cargarHistorialEventos();
        });

        function cargarHistorialCargas() {
            const tbody = document.getElementById('historialCargasTbody');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Cargando...</td></tr>';

            const params = new URLSearchParams();
            params.set('page', historialState.page);
            params.set('perPage', historialState.perPage);
            if (historialState.eventoId) params.set('evento_id', historialState.eventoId);

            fetch(`${API.historial}?${params.toString()}`)
                .then(res => res.json())
                .then(data => {
                    historialState.page = data.page;
                    historialState.totalPages = data.totalPages;
                    renderHistorialCargasTabla(data.data);
                    renderHistorialCargasPaginacion(data);
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Error al cargar el historial: ${err.message}</td></tr>`;
                });
        }

        function renderHistorialCargasTabla(cargas) {
            const tbody = document.getElementById('historialCargasTbody');

            if (cargas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Todavía no hay cargas registradas.</td></tr>';
                return;
            }

            tbody.innerHTML = cargas.map(c => `
                <tr>
                    <td>${formatearFecha(c.created_at)}</td>
                    <td>${c.nombre_archivo || '—'}</td>
                    <td>${c.evento_nombre ? c.evento_nombre : '<span class="text-muted">Sin evento</span>'}</td>
                    <td>${c.total_filas}</td>
                    <td><span class="badge bg-success">${c.insertados}</span></td>
                    <td><span class="badge bg-info text-dark">${c.actualizados}</span></td>
                    <td>${c.usuario_nombre || '—'}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-dark historial-ver-contactos-btn" data-id="${c.id}">
                            <i class="bx bx-show me-1"></i> Ver contactos
                        </button>
                    </td>
                </tr>
            `).join('');

            document.querySelectorAll('.historial-ver-contactos-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    abrirExplorerPorImportacion(parseInt(this.dataset.id));
                });
            });
        }

        function renderHistorialCargasPaginacion(data) {
            const pag = document.getElementById('historialCargasPagination');
            const info = document.getElementById('historialCargasPageInfo');
            const { page, totalPages, total, perPage } = data;

            info.textContent = total === 0
                ? 'Sin resultados'
                : `Página ${page} de ${totalPages} — ${total} carga(s) en total`;

            if (totalPages <= 1) {
                pag.innerHTML = '';
                return;
            }

            const botones = [];
            const addBtn = (label, target, disabled = false, active = false) => {
                botones.push(`<li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
                    <button type="button" class="page-link historial-page-btn" data-page="${target}">${label}</button>
                </li>`);
            };

            addBtn('«', 1, page === 1);
            addBtn('‹', page - 1, page === 1);
            let start = Math.max(1, page - 2);
            let end = Math.min(totalPages, page + 2);
            for (let p = start; p <= end; p++) addBtn(p, p, false, p === page);
            addBtn('›', page + 1, page === totalPages);
            addBtn('»', totalPages, page === totalPages);

            pag.innerHTML = botones.join('');
            pag.querySelectorAll('.historial-page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = parseInt(this.dataset.page);
                    if (target >= 1 && target <= historialState.totalPages && target !== historialState.page) {
                        historialState.page = target;
                        cargarHistorialCargas();
                    }
                });
            });
        }

        document.getElementById('historialFiltroEvento').addEventListener('change', function() {
            historialState.eventoId = this.value;
            historialState.page = 1;
            cargarHistorialCargas();
        });

        // Abre el Explorador de Datos ya filtrado con los contactos que trajo
        // una carga puntual del historial (usa la bitácora contacto_importaciones,
        // así que es exacto aunque esos contactos hayan sido tocados después
        // por otra carga distinta).
        function abrirExplorerPorImportacion(importacionId) {
            explorerState.filtros = {
                term: '', pais: '', empresa: '', evento_id: '',
                reglas: [{ campo: 'importacion_id', operador: 'igual', valor: String(importacionId) }],
            };
            explorerState.page = 1;
            document.getElementById('explorerTerm').value = '';
            document.getElementById('explorerPais').value = '';
            document.getElementById('explorerEmpresa').value = '';
            document.getElementById('explorerEvento').value = '';
            historialModal.hide();
            explorerModal.show();
            if (Object.keys(allColumnsMap).length === 0) {
                cargarColumnas().then(cargarExplorerPaises).then(() => cargarExplorerDatos());
            } else {
                cargarExplorerPaises().then(() => cargarExplorerDatos());
            }
        }

        // Abre el Explorador de Datos filtrado por "todos los contactos que
        // alguna vez asistieron a este evento" (histórico completo, vía la
        // misma bitácora, no solo el último evento registrado en el contacto).
        function abrirExplorerPorEvento(eventoId) {
            explorerState.filtros = { term: '', pais: '', empresa: '', evento_id: String(eventoId), reglas: [] };
            explorerState.page = 1;
            document.getElementById('explorerTerm').value = '';
            document.getElementById('explorerPais').value = '';
            document.getElementById('explorerEmpresa').value = '';
            historialModal.hide();
            explorerModal.show();
            const setEventoYcargar = () => {
                const sel = document.getElementById('explorerEvento');
                if (sel) sel.value = String(eventoId);
                cargarExplorerDatos();
            };
            if (Object.keys(allColumnsMap).length === 0) {
                cargarColumnas().then(cargarExplorerPaises).then(setEventoYcargar);
            } else {
                cargarExplorerPaises().then(setEventoYcargar);
            }
        }

        function cargarHistorialEventos() {
            const tbody = document.getElementById('historialEventosTbody');
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Cargando...</td></tr>';

            fetch(API.eventos)
                .then(res => res.json())
                .then(data => {
                    eventosCache = data;
                    renderHistorialEventosTabla(data);
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar eventos: ${err.message}</td></tr>`;
                });
        }

        function renderHistorialEventosTabla(eventos) {
            const tbody = document.getElementById('historialEventosTbody');

            if (eventos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Todavía no has creado ningún evento.</td></tr>';
                return;
            }

            tbody.innerHTML = eventos.map(ev => `
                <tr>
                    <td>
                        <strong>${ev.nombre}</strong>
                        ${ev.descripcion ? `<br><span class="text-muted small">${ev.descripcion}</span>` : ''}
                    </td>
                    <td>${ev.fecha_evento ? formatearFechaSolo(ev.fecha_evento) : '—'}</td>
                    <td>${ev.lugar || '—'}</td>
                    <td><span class="badge bg-primary">${ev.total_contactos}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-dark historial-ver-evento-btn" data-id="${ev.id}">
                            <i class="bx bx-show me-1"></i> Ver contactos
                        </button>
                    </td>
                </tr>
            `).join('');

            document.querySelectorAll('.historial-ver-evento-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    abrirExplorerPorEvento(parseInt(this.dataset.id));
                });
            });
        }

        document.getElementById('crearEventoButton').addEventListener('click', function() {
            const nombre = document.getElementById('nuevoEventoNombre').value.trim();
            const fecha = document.getElementById('nuevoEventoFecha').value;
            const lugar = document.getElementById('nuevoEventoLugar').value.trim();

            if (nombre === '') {
                showMessage('Escribe el nombre del evento.', 'error');
                return;
            }

            const btn = this;
            btn.disabled = true;

            fetch(API.crearEvento, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre, fecha_evento: fecha, lugar }),
                })
                .then(res => {
                    if (!res.ok) return res.json().then(err => { throw new Error(err.message || 'Error al crear el evento'); });
                    return res.json();
                })
                .then(() => {
                    document.getElementById('nuevoEventoNombre').value = '';
                    document.getElementById('nuevoEventoFecha').value = '';
                    document.getElementById('nuevoEventoLugar').value = '';
                    showMessage('Evento creado exitosamente', 'success');
                    return cargarEventos();
                })
                .then(() => cargarHistorialEventos())
                .catch(err => showMessage('Error al crear evento: ' + err.message, 'error'))
                .finally(() => { btn.disabled = false; });
        });

        function formatearFecha(mysqlDatetime) {
            if (!mysqlDatetime) return '—';
            const d = new Date(mysqlDatetime.replace(' ', 'T'));
            if (isNaN(d.getTime())) return mysqlDatetime;
            return d.toLocaleString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        function formatearFechaSolo(mysqlDate) {
            if (!mysqlDate) return '—';
            const d = new Date(mysqlDate.includes('T') ? mysqlDate : mysqlDate + 'T00:00:00');
            if (isNaN(d.getTime())) return mysqlDate;
            return d.toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

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

        filtroEvento.addEventListener('change', function() {
            ejecutarBusqueda(searchInput.value.trim());
        });

        limpiarFiltrosButton.addEventListener('click', function() {
            searchInput.value = '';
            filtroPais.value = '';
            filtroEvento.value = '';
            $('#filtroEmpresa').val(null).trigger('change');
            ejecutarBusqueda('');
        });

        // ---------- Búsqueda en tiempo real (texto + filtros) ----------
        function ejecutarBusqueda(term) {
            const pais = filtroPais.value;
            const empresa = $('#filtroEmpresa').val() || '';
            const eventoId = filtroEvento.value;

            if (!term && !pais && !empresa && !eventoId) {
                resultsDiv.innerHTML = '<p class="text-muted text-center py-4">Empieza a escribir o usa los filtros para buscar.</p>';
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
            if (eventoId) params.set('evento_id', eventoId);

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
                resultsDiv.innerHTML = '<p class="text-muted text-center py-4">No se encontraron resultados.</p>';
                return;
            }

            const tempList = getTempList();
            const tempListIds = tempList.map(c => c.id.toString());

            const fields = [
                'nombres_y_apellidos_completos', 'empresa', 'cargo', 'correo_corporativo',
                'correo_electronico', 'celular', 'telefono', 'linkedin', 'pais', 'evento_nombre',
                'nota_origen', 'status_correo_corporativo'
            ];
            const headers = [
                'Nombres y Apellidos', 'Empresa', 'Cargo', 'Correo Corporativo',
                'Correo Personal', 'Celular', 'Teléfono', 'LinkedIn', 'País', 'Evento', 'Nota', 'Status Correo'
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
                        <button class="btn btn-sm btn-outline-danger correo-single-btn" data-id="${r.id}" data-sincorreo="${!correoDeContacto(r) ? '1' : '0'}" title="Enviar correo a este contacto"><i class="bx bx-envelope"></i></button>
                        ${isInTemp ? `<button class="btn btn-sm btn-outline-danger ms-1 remove-temp-btn" data-id="${r.id}">Quitar</button>` : ''}
                    </td>
                    ${fields.map(f => `<td title="${(r[f] || '').toString().replace(/"/g, '&quot;')}">${r[f] || ''}</td>`).join('')}
                </tr>
            `;
            }).join('');

            resultsDiv.innerHTML = `
            <p class="text-muted small mb-2">${results.length} resultado(s)${results.length === 500 ? ' (mostrando los primeros 500, afina tu búsqueda para ver menos)' : ''}</p>
            <div class="table-responsive" style="max-height:600px;">
                <table class="table table-striped table-hover align-middle">
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

            document.querySelectorAll('.correo-single-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.dataset.sincorreo === '1') {
                        showMessage('Este contacto no tiene correo corporativo ni personal registrado, no se le puede enviar correo.', 'error');
                        return;
                    }
                    const contact = globalResults.find(c => c.id.toString() === this.dataset.id);
                    if (contact) abrirModalCorreos([contact], [contact.id]);
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
            body.innerHTML = '<p class="text-muted text-center py-4"><span class="spinner-border spinner-border-sm me-2"></span> Buscando duplicados...</p>';
            resumen.textContent = '';

            fetch(API.duplicados)
                .then(res => res.json())
                .then(data => {
                    if (!data.grupos || data.grupos.length === 0) {
                        body.innerHTML = '<p class="text-muted text-center py-4">No se encontraron contactos duplicados. 🎉</p>';
                        return;
                    }

                    resumen.textContent = `${data.totalGrupos} grupo(s) de duplicados${data.truncado ? ' (mostrando los primeros 200)' : ''}`;

                    body.innerHTML = data.grupos.map((grupo, gi) => `
                    <div class="card mb-3">
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
                    body.innerHTML = `<p class="text-danger text-center py-4">Error al buscar duplicados: ${err.message}</p>`;
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
            filtros: { term: '', pais: '', empresa: '', evento_id: '', reglas: [] },
            data: [],
            total: 0,
            totalPages: 0,
            selectedIds: new Set(),
        };
        // Guarda los datos completos de cada contacto que se haya visto/seleccionado en el explorador,
        // para poder enviarle correo aunque ya haya cambiado de página.
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
            if (filtros.evento_id) params.set('evento_id', filtros.evento_id);
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
                        `(${data.total.toLocaleString('es-PE')} contacto(s) coinciden)`;
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
                            <button class="btn btn-sm btn-outline-danger explorer-correo-btn" data-id="${r.id}" data-sincorreo="${!correoDeContacto(r) ? '1' : '0'}" title="Enviar correo a este contacto"><i class="bx bx-envelope"></i></button>
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

            document.querySelectorAll('.explorer-correo-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.dataset.sincorreo === '1') {
                        showMessage('Este contacto no tiene correo corporativo ni personal registrado, no se le puede enviar correo.', 'error');
                        return;
                    }
                    const contact = explorerState.data.find(c => c.id.toString() === this.dataset.id);
                    if (contact) {
                        explorerModal.hide();
                        abrirModalCorreos([contact], [contact.id]);
                    }
                });
            });

            updateExplorerSeleccionLabel();
        }

        function updateExplorerSeleccionLabel() {
            const n = explorerState.selectedIds.size;
            document.getElementById('explorerSeleccionLabel').textContent = `${n} seleccionado(s) (en todas las páginas)`;
            document.getElementById('explorerEliminarSeleccionadosButton').disabled = n === 0;
            document.getElementById('explorerEnviarCorreoButton').disabled = n === 0;
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
            explorerState.filtros.evento_id = document.getElementById('explorerEvento').value;
            explorerState.page = 1;
            cargarExplorerDatos();
        });

        document.getElementById('explorerLimpiarButton').addEventListener('click', function() {
            document.getElementById('explorerTerm').value = '';
            document.getElementById('explorerPais').value = '';
            document.getElementById('explorerEmpresa').value = '';
            document.getElementById('explorerEvento').value = '';
            explorerState.filtros = { term: '', pais: '', empresa: '', evento_id: '', reglas: [] };
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

        document.getElementById('explorerEnviarCorreoButton').addEventListener('click', function() {
            const ids = Array.from(explorerState.selectedIds);
            if (ids.length === 0) return;

            const contactos = ids.map(id => explorerContactosCache.get(id)).filter(Boolean);

            if (contactos.length < ids.length) {
                showMessage('Algunos contactos seleccionados ya no están en memoria (cambiaste de página sin verlos). Se usarán solo los disponibles.', 'error');
            }

            if (contactos.length === 0) {
                showMessage('No se pudo recuperar la información de los contactos seleccionados. Vuelve a seleccionarlos.', 'error');
                return;
            }

            explorerModal.hide();
            abrirModalCorreos(contactos, contactos.map(c => c.id));
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
                        ${Object.entries(allColumnsMap).filter(([field]) => field !== 'evento_nombre').map(([field, label]) => `<option value="${field}">${label}</option>`).join('')}
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
            explorerState.filtros = { term: '', pais: '', empresa: '', evento_id: '', reglas };
            explorerState.page = 1;
            document.getElementById('explorerTerm').value = '';
            document.getElementById('explorerPais').value = '';
            document.getElementById('explorerEmpresa').value = '';
            document.getElementById('explorerEvento').value = '';
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

        // ======================================================
        // ---------- Enviar Correos (Mailgun) ----------
        // ======================================================
        const correosModalEl = document.getElementById('correosModal');
        const correosModal = new bootstrap.Modal(correosModalEl);
        let correoImagenPath = '';
        let correoPdfPath = '';
        const correosSeleccionados = new Set();
        let correosCuota = { limite: 50, enviados: 0, disponibles: 50, proximaLiberacion: null, cargada: false };
        let correosPool = []; // de dónde salen los destinatarios disponibles (búsqueda principal, explorador, o un solo contacto)

        /**
         * Punto de entrada ÚNICO para abrir el modal de correos, sin importar desde dónde se llame
         * (botón general, explorador, o el ícono de correo de una fila puntual).
         * @param {Array} contactos       Contactos disponibles para elegir en este envío.
         * @param {Array} preseleccionarIds  Ids que ya deben venir marcados al abrir el modal.
         */
        function abrirModalCorreos(contactos, preseleccionarIds = []) {
            if (!contactos || contactos.length === 0) {
                showMessage('No hay contactos disponibles para enviar correo.', 'error');
                return;
            }

            correosPool = contactos;
            correosSeleccionados.clear();
            preseleccionarIds.forEach(id => correosSeleccionados.add(id));

            document.getElementById('correoVistaPreviaPanel').style.display = 'none';
            document.getElementById('correosSoloSeleccionadosToggle').checked = preseleccionarIds.length > 0;
            document.getElementById('correosOrigenTexto').textContent = preseleccionarIds.length > 0
                ? 'Estos son los contactos que traías seleccionados. Puedes destildar el filtro de abajo para elegir otros de la búsqueda actual.'
                : 'Esta lista toma los contactos que tengas cargados arriba en "Búsqueda de Registros". Si no hay resultados, primero busca o filtra contactos.';

            renderCorreosDestinatarios();
            cargarCuotaCorreos();
            correosModal.show();
        }

        document.getElementById('openCorreosButton').addEventListener('click', function() {
            if (globalResults.length === 0) {
                showMessage('Primero realiza una búsqueda para tener contactos disponibles para enviar correo.', 'error');
                return;
            }
            abrirModalCorreos(globalResults, []);
        });

        document.getElementById('correosSoloSeleccionadosToggle').addEventListener('change', renderCorreosDestinatarios);

        function cargarCuotaCorreos() {
            const info = document.getElementById('correosCuotaInfo');
            info.className = 'alert alert-info small mb-3';
            info.textContent = 'Cargando cupo diario...';
            correosCuota.cargada = false;

            fetch(base_url + 'contactos/correos/cuota')
                .then(res => res.json().then(data => ({ ok: res.ok, data })))
                .then(({ ok, data }) => {
                    const esperadas = ['limite', 'enviados', 'disponibles'];
                    const formatoValido = data && esperadas.every(k => typeof data[k] === 'number');

                    if (!ok || !formatoValido) {
                        correosCuota = { limite: 0, enviados: 0, disponibles: 0, proximaLiberacion: null, cargada: false };
                        info.className = 'alert alert-danger small mb-3';
                        info.textContent = 'No se pudo leer el cupo diario. Verifica que hayas ejecutado la migración '
                            + '(php spark migrate) y que la ruta contactos/correos/cuota exista en Routes.php. '
                            + 'Respuesta recibida: ' + JSON.stringify(data);
                        return;
                    }

                    correosCuota = {
                        limite: data.limite,
                        enviados: data.enviados,
                        disponibles: data.disponibles,
                        proximaLiberacion: data.proximaLiberacion || null,
                        cargada: true,
                    };

                    info.textContent = `Cupo diario: ${data.enviados}/${data.limite} usados en las últimas 24 horas. Disponibles ahora: ${data.disponibles}.` +
                        (data.disponibles === 0 && data.proximaLiberacion ? ` Se libera cupo el ${data.proximaLiberacion}.` : '');
                    info.className = 'alert small mb-3 ' + (data.disponibles === 0 ? 'alert-danger' : 'alert-info');
                })
                .catch(err => {
                    correosCuota = { limite: 0, enviados: 0, disponibles: 0, proximaLiberacion: null, cargada: false };
                    info.className = 'alert alert-danger small mb-3';
                    info.textContent = 'Error de conexión al consultar el cupo diario: ' + err.message;
                });
        }

        /**
         * Único punto de verdad para decidir si un contacto tiene correo utilizable.
         * Prioriza correo_corporativo, luego correo_electronico. Ignora espacios en blanco.
         * Debe reflejar la misma regla que usa el backend (EnvioCorreoController::enviar).
         */
        function correoDeContacto(c) {
            const corporativo = (c.correo_corporativo || '').trim();
            if (corporativo !== '') return corporativo;
            const personal = (c.correo_electronico || '').trim();
            return personal !== '' ? personal : '';
        }

        function renderCorreosDestinatarios() {
            const body = document.getElementById('correosDestinatariosBody');
            document.getElementById('correosTotalLabel').textContent = correosPool.length;

            const soloSeleccionados = document.getElementById('correosSoloSeleccionadosToggle').checked;
            const lista = soloSeleccionados ? correosPool.filter(c => correosSeleccionados.has(c.id)) : correosPool;

            if (lista.length === 0) {
                body.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">${soloSeleccionados ? 'No has seleccionado ningún contacto todavía.' : 'Sin contactos disponibles.'}</td></tr>`;
            } else {
                body.innerHTML = lista.map(c => {
                    const correo = correoDeContacto(c);
                    const estado = c.estado_envio_correo || 'pendiente';
                    const badgeClase = estado === 'enviado' ? 'bg-success' : (estado === 'error' ? 'bg-danger' : 'bg-secondary');
                    return `<tr class="${!correo ? 'text-muted' : ''}">
                        <td><input type="checkbox" class="form-check-input correo-row-checkbox" data-id="${c.id}" data-sincorreo="${!correo ? '1' : '0'}" title="${!correo ? 'Este contacto no tiene correo corporativo ni personal registrado' : ''}" ${correosSeleccionados.has(c.id) ? 'checked' : ''}></td>
                        <td>${c.nombres_y_apellidos_completos || ''}</td>
                        <td>${c.empresa || ''}</td>
                        <td>${correo || '<span class="text-danger">sin correo</span>'}</td>
                        <td><span class="badge ${badgeClase}">${estado}</span></td>
                    </tr>`;
                }).join('');
            }

            document.querySelectorAll('.correo-row-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const id = parseInt(this.dataset.id);
                    if (this.checked) {
                        if (this.dataset.sincorreo === '1') {
                            this.checked = false;
                            showMessage('Este contacto no tiene correo corporativo ni personal registrado, no se le puede enviar correo.', 'error');
                            return;
                        }
                        if (correosCuota.cargada && correosSeleccionados.size >= correosCuota.disponibles) {
                            this.checked = false;
                            showMessage(`Ya alcanzaste el cupo disponible (${correosCuota.disponibles}). No se pueden seleccionar más para evitar bloqueos por spam.`, 'error');
                            return;
                        }
                        correosSeleccionados.add(id);
                    } else {
                        correosSeleccionados.delete(id);
                        if (soloSeleccionados) renderCorreosDestinatarios(); // desaparece de la lista filtrada
                    }
                    updateCorreosContador();
                });
            });

            updateCorreosContador();
        }

        function updateCorreosContador() {
            document.getElementById('correosContadorLabel').textContent = correosSeleccionados.size;
            document.getElementById('correosEnviarButton').disabled = correosSeleccionados.size === 0;
        }

        /**
         * Agrega ids a la selección respetando el cupo disponible (máx. 50/24h por defecto).
         * Devuelve cuántos se quedaron fuera por falta de cupo.
         */
        function agregarConLimiteDeCuota(candidatos) {
            if (!correosCuota.cargada) {
                showMessage('Aún no se cargó el cupo diario, espera un segundo e inténtalo de nuevo.', 'error');
                return 0;
            }

            let disponiblesRestantes = correosCuota.disponibles - correosSeleccionados.size;
            let excedentes = 0;

            for (const id of candidatos) {
                if (correosSeleccionados.has(id)) continue;
                if (disponiblesRestantes <= 0) {
                    excedentes++;
                    continue;
                }
                correosSeleccionados.add(id);
                disponiblesRestantes--;
            }

            return excedentes;
        }

        document.getElementById('correosSeleccionarTodoButton').addEventListener('click', function() {
            const candidatos = correosPool.filter(c => correoDeContacto(c) !== '').map(c => c.id);
            const excedentes = agregarConLimiteDeCuota(candidatos);
            renderCorreosDestinatarios();
            if (excedentes > 0) {
                showMessage(`Se seleccionaron hasta el cupo diario disponible (${correosCuota.disponibles}). ${excedentes} contacto(s) quedaron sin seleccionar por el límite, para evitar bloqueos por spam.`, 'error');
            }
        });

        document.getElementById('correosSeleccionarPendientesButton').addEventListener('click', function() {
            const candidatos = correosPool
                .filter(c => correoDeContacto(c) !== '' && c.estado_envio_correo !== 'enviado')
                .map(c => c.id);
            const excedentes = agregarConLimiteDeCuota(candidatos);
            renderCorreosDestinatarios();
            if (excedentes > 0) {
                showMessage(`Se seleccionaron hasta el cupo diario disponible (${correosCuota.disponibles}). ${excedentes} contacto(s) pendientes quedaron sin seleccionar por el límite, para evitar bloqueos por spam.`, 'error');
            }
        });

        document.getElementById('correosDeseleccionarButton').addEventListener('click', function() {
            correosSeleccionados.clear();
            renderCorreosDestinatarios();
        });

        document.getElementById('correoImagenInput').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const fd = new FormData();
            fd.append('archivo', file);
            fd.append('tipo', 'imagen');

            fetch(base_url + 'contactos/correos/subir-archivo', { method: 'POST', body: fd })
                .then(res => res.json().then(data => ({ ok: res.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok) throw new Error(data.message || 'Error al subir la imagen');
                    correoImagenPath = data.path;
                    document.getElementById('correoImagenPreview').innerHTML =
                        `<img src="${data.url}" style="max-height:80px;" class="border rounded"> <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="correoImagenQuitar">Quitar</button>`;
                    document.getElementById('correoImagenQuitar').addEventListener('click', function() {
                        correoImagenPath = '';
                        document.getElementById('correoImagenPreview').innerHTML = '';
                        document.getElementById('correoImagenInput').value = '';
                    });
                })
                .catch(err => showMessage(err.message, 'error'));
        });

        document.getElementById('correoPdfInput').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const fd = new FormData();
            fd.append('archivo', file);
            fd.append('tipo', 'pdf');

            fetch(base_url + 'contactos/correos/subir-archivo', { method: 'POST', body: fd })
                .then(res => res.json().then(data => ({ ok: res.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok) throw new Error(data.message || 'Error al subir el PDF');
                    correoPdfPath = data.path;
                    document.getElementById('correoPdfPreview').innerHTML =
                        `📎 ${file.name} <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="correoPdfQuitar">Quitar</button>`;
                    document.getElementById('correoPdfQuitar').addEventListener('click', function() {
                        correoPdfPath = '';
                        document.getElementById('correoPdfPreview').innerHTML = '';
                        document.getElementById('correoPdfInput').value = '';
                    });
                })
                .catch(err => showMessage(err.message, 'error'));
        });

        document.getElementById('correoVistaPreviaButton').addEventListener('click', function() {
            const asunto = document.getElementById('correoAsunto').value.trim();
            const cuerpo = document.getElementById('correoCuerpo').value.trim();

            if (!asunto && !cuerpo && !correoImagenPath) {
                showMessage('Completa al menos el asunto, el cuerpo o la imagen para ver la vista previa', 'error');
                return;
            }

            // Usa el primer contacto seleccionado (con datos reales) o un contacto de ejemplo.
            let nombre = 'Juan Pérez';
            let empresa = 'Empresa Ejemplo S.A.C.';
            let correoDestino = 'ejemplo@correo.com';

            if (correosSeleccionados.size > 0) {
                const primerId = Array.from(correosSeleccionados)[0];
                const contacto = correosPool.find(c => c.id === primerId);
                if (contacto) {
                    nombre = contacto.nombres_y_apellidos_completos || nombre;
                    empresa = contacto.empresa || empresa;
                    correoDestino = correoDeContacto(contacto) || correoDestino;
                }
            }

            const asuntoFinal = asunto.replace(/\{\{nombre\}\}/g, nombre).replace(/\{\{empresa\}\}/g, empresa);
            let cuerpoFinal = cuerpo.replace(/\{\{nombre\}\}/g, nombre).replace(/\{\{empresa\}\}/g, empresa);

            const imgPreviewEl = document.querySelector('#correoImagenPreview img');
            if (imgPreviewEl) {
                cuerpoFinal = `<img src="${imgPreviewEl.src}" style="max-width:100%; height:auto;" alt=""><br><br>` + cuerpoFinal;
            }

            document.getElementById('correoVistaPara').textContent = correoDestino;
            document.getElementById('correoVistaAsunto').textContent = asuntoFinal || '(sin asunto)';

            const adjuntoWrap = document.getElementById('correoVistaAdjuntoWrap');
            if (correoPdfPath) {
                adjuntoWrap.style.display = 'block';
                document.getElementById('correoVistaAdjunto').textContent = correoPdfPath.split('/').pop();
            } else {
                adjuntoWrap.style.display = 'none';
            }

            const frame = document.getElementById('correoVistaFrame');
            frame.srcdoc = `<div style="font-family:Arial, sans-serif; padding:12px; color:#212529;">${cuerpoFinal || '<em>(sin cuerpo)</em>'}</div>`;

            document.getElementById('correoVistaPreviaPanel').style.display = 'block';
        });

        document.getElementById('correosEnviarButton').addEventListener('click', function() {
            const asunto = document.getElementById('correoAsunto').value.trim();
            const cuerpo = document.getElementById('correoCuerpo').value.trim();

            if (!asunto) { showMessage('El asunto es obligatorio', 'error'); return; }
            if (!cuerpo && !correoImagenPath) { showMessage('Agrega un cuerpo de correo o una imagen', 'error'); return; }
            if (correosSeleccionados.size === 0) { showMessage('Selecciona al menos un destinatario', 'error'); return; }
            if (correosCuota.cargada && correosSeleccionados.size > correosCuota.disponibles) {
                showMessage(`Tu selección (${correosSeleccionados.size}) supera el cupo disponible (${correosCuota.disponibles}). Quita algunos destinatarios o espera a que se libere cupo.`, 'error');
                return;
            }

            Swal.fire({
                title: `¿Enviar correo a ${correosSeleccionados.size} contacto(s)?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;

                const btn = document.getElementById('correosEnviarButton');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando...';

                fetch(base_url + 'contactos/correos/enviar', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            asunto,
                            cuerpoHtml: cuerpo,
                            imagenPath: correoImagenPath,
                            pdfPath: correoPdfPath,
                            contactoIds: Array.from(correosSeleccionados),
                        })
                    })
                    .then(res => res.json().then(data => ({ ok: res.ok, data })))
                    .then(({ ok, data }) => {
                        if (!ok) throw new Error(data.message || 'Error al enviar');

                        (data.detalles || []).forEach(d => {
                            if (!d.estado) return;
                            [globalResults, correosPool, explorerState.data].forEach(lista => {
                                const c = lista.find(x => x.id === d.id);
                                if (c) c.estado_envio_correo = d.estado;
                            });
                            const cacheado = explorerContactosCache.get(d.id);
                            if (cacheado) cacheado.estado_envio_correo = d.estado;
                        });

                        correosSeleccionados.clear();
                        cargarCuotaCorreos();
                        mostrarResumenEnvio(data);
                    })
                    .catch(err => showMessage('Error al enviar correos: ' + err.message, 'error'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bx bx-send me-1"></i> Enviar Correo';
                    });
            });
        });

        /**
         * Muestra el resumen final del envío (cuántos ok, fallidos, sin correo, pendientes por cupo)
         * con el detalle por contacto, y cierra el modal de correos al confirmar.
         */
        function mostrarResumenEnvio(data) {
            const detalles = data.detalles || [];

            const listaHtml = detalles.map(d => {
                const nombre = d.nombre || `Contacto #${d.id}`;
                if (d.estado === 'enviado') {
                    return `<div class="small border-bottom py-1">✅ <strong>${nombre}</strong> — enviado a ${d.correo || ''}</div>`;
                }
                if (d.estado === 'error') {
                    return `<div class="small border-bottom py-1">❌ <strong>${nombre}</strong> — falló${d.error ? ': ' + d.error : ''}</div>`;
                }
                return `<div class="small border-bottom py-1">⚠️ <strong>${nombre}</strong> — sin correo registrado</div>`;
            }).join('');

            const huboProblemas = data.fallidos > 0 || data.sinCorreo > 0 || data.pendientesPorCuota > 0;
            const icono = data.enviados === 0 ? 'error' : (huboProblemas ? 'warning' : 'success');
            const titulo = data.enviados === 0 ? 'No se pudo enviar ningún correo' : (huboProblemas ? 'Envío completado con observaciones' : '¡Correos enviados!');

            Swal.fire({
                icon: icono,
                title: titulo,
                html: `
                    <div class="text-start mb-2">
                        <div>✅ Enviados: <strong>${data.enviados}</strong></div>
                        <div>❌ Fallidos: <strong>${data.fallidos}</strong></div>
                        <div>⚠️ Sin correo registrado: <strong>${data.sinCorreo}</strong></div>
                        ${data.pendientesPorCuota > 0 ? `<div>⏳ Pendientes por cupo diario: <strong>${data.pendientesPorCuota}</strong></div>` : ''}
                    </div>
                    ${listaHtml ? `<div class="text-start" style="max-height:220px; overflow-y:auto; border-top:1px solid #eee; padding-top:8px;">${listaHtml}</div>` : ''}
                `,
                confirmButtonText: 'Entendido',
            }).then(() => {
                renderCorreosDestinatarios();
                correosModal.hide();
            });
        }

        // ---------- Total de contactos en la BD (informativo) ----------
    function cargarTotalContactos() {
        fetch(base_url + 'contactos/total')
            .then(res => res.json())
            .then(data => {
                const label = document.getElementById('totalContactosLabel');
                if (label) {
                    label.textContent = `(${data.total.toLocaleString('es-PE')} contactos en total)`;
                }
            })
            .catch(() => {
                const label = document.getElementById('totalContactosLabel');
                if (label) label.textContent = '';
            });
    }

    cargarTotalContactos();

    })();
</script>
<?= $this->endSection() ?>