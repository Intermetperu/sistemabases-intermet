<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContactoModel;

class ContactoController extends BaseController
{
    protected ContactoModel $contactoModel;

    public function __construct()
    {
        $this->contactoModel = new ContactoModel();
    }

    public function index()
    {
        return view('admin/contactos/index');
    }

    public function buscar()
    {
        $term    = trim((string) $this->request->getGet('term'));
        $pais    = trim((string) $this->request->getGet('pais'));
        $empresa = trim((string) $this->request->getGet('empresa'));

        if ($term === '' && $pais === '' && $empresa === '') {
            return $this->response->setJSON([]);
        }

        return $this->response->setJSON($this->contactoModel->buscar($term, $pais, $empresa));
    }

    public function paises()
    {
        return $this->response->setJSON($this->contactoModel->paisesDistintos());
    }

    public function empresasBuscar()
    {
        $q = trim((string) $this->request->getGet('q'));
        return $this->response->setJSON($this->contactoModel->empresasComoOpciones($q));
    }

    /**
     * Detecta grupos de contactos duplicados por número de documento,
     * correo personal o correo corporativo.
     */
    public function duplicados()
    {
        ini_set('max_execution_time', 120);

        $porDocumento = $this->contactoModel
            ->select('nro_documento')
            ->where('nro_documento IS NOT NULL')
            ->where('nro_documento !=', '')
            ->groupBy('nro_documento')
            ->having('COUNT(id) >', 1)
            ->findAll(300);

        $porCorreoPersonal = $this->contactoModel
            ->select('correo_electronico')
            ->where('correo_electronico IS NOT NULL')
            ->where('correo_electronico !=', '')
            ->groupBy('correo_electronico')
            ->having('COUNT(id) >', 1)
            ->findAll(300);

        $porCorreoCorp = $this->contactoModel
            ->select('correo_corporativo')
            ->where('correo_corporativo IS NOT NULL')
            ->where('correo_corporativo !=', '')
            ->groupBy('correo_corporativo')
            ->having('COUNT(id) >', 1)
            ->findAll(300);

        $campos = 'id, nombres_y_apellidos_completos, empresa, cargo, correo_electronico,
                   correo_corporativo, nro_documento, created_at';

        $grupos = [];

        foreach ($porDocumento as $row) {
            $grupos[] = [
                'criterio'  => 'Nº Documento',
                'valor'     => $row['nro_documento'],
                'contactos' => $this->contactoModel->select($campos)
                    ->where('nro_documento', $row['nro_documento'])
                    ->orderBy('id', 'DESC')
                    ->findAll(),
            ];
        }

        foreach ($porCorreoPersonal as $row) {
            $grupos[] = [
                'criterio'  => 'Correo personal',
                'valor'     => $row['correo_electronico'],
                'contactos' => $this->contactoModel->select($campos)
                    ->where('correo_electronico', $row['correo_electronico'])
                    ->orderBy('id', 'DESC')
                    ->findAll(),
            ];
        }

        foreach ($porCorreoCorp as $row) {
            $grupos[] = [
                'criterio'  => 'Correo corporativo',
                'valor'     => $row['correo_corporativo'],
                'contactos' => $this->contactoModel->select($campos)
                    ->where('correo_corporativo', $row['correo_corporativo'])
                    ->orderBy('id', 'DESC')
                    ->findAll(),
            ];
        }

        // Evitar mostrar el mismo grupo de IDs dos veces (si coincide por documento Y por correo)
        $vistos = [];
        $gruposUnicos = [];
        foreach ($grupos as $g) {
            $ids = array_column($g['contactos'], 'id');
            sort($ids);
            $firma = implode('-', $ids);
            if (isset($vistos[$firma])) {
                continue;
            }
            $vistos[$firma] = true;
            $gruposUnicos[] = $g;
        }

        // Límite de seguridad para no saturar el navegador si hay demasiados duplicados
        $truncado = count($gruposUnicos) > 200;
        $gruposUnicos = array_slice($gruposUnicos, 0, 200);

        return $this->response->setJSON([
            'totalGrupos' => count($gruposUnicos),
            'truncado'    => $truncado,
            'grupos'      => $gruposUnicos,
        ]);
    }

    public function subirCsv()
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $file = $this->request->getFile('csvFile');

        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'No se recibió un archivo CSV válido.',
            ]);
        }

        $mapping = ContactoModel::columnMapping();

        $normalize = function (string $text): string {
            $text = trim($text);
            $text = mb_strtolower($text, 'UTF-8');
            $translit = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($translit !== false) {
                $text = $translit;
            }
            $text = preg_replace('/\s+/', ' ', $text);
            return trim($text);
        };

        $normalizedMap = [];
        foreach ($mapping as $csvHeader => $dbColumn) {
            $normalizedMap[$normalize($csvHeader)] = $dbColumn;
        }

        $path = $file->getTempName();

        $probe = fopen($path, 'r');
        $firstLine = fgets($probe);
        fclose($probe);

        if ($firstLine === false) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El archivo CSV está vacío.',
            ]);
        }

        if (substr($firstLine, 0, 3) === "\xEF\xBB\xBF") {
            $firstLine = substr($firstLine, 3);
        }

        $counts = [
            ';'  => substr_count($firstLine, ';'),
            ','  => substr_count($firstLine, ','),
            "\t" => substr_count($firstLine, "\t"),
        ];
        arsort($counts);
        $delimiter = array_key_first($counts);
        if ($counts[$delimiter] === 0) {
            $delimiter = ';';
        }

        $handle = fopen($path, 'r');

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $rawHeader = fgetcsv($handle, 0, $delimiter);

        if ($rawHeader === false) {
            fclose($handle);
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'El archivo CSV no tiene un formato válido.',
            ]);
        }

        $columnIndexToDbField = [];
        $headersDetectados = [];

        foreach ($rawHeader as $i => $headerCell) {
            $headerCell = trim((string) $headerCell);
            $headersDetectados[] = $headerCell;
            $norm = $normalize($headerCell);

            if (isset($normalizedMap[$norm])) {
                $columnIndexToDbField[$i] = $normalizedMap[$norm];
            }
        }

        if (count($columnIndexToDbField) === 0) {
            fclose($handle);
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'No se reconoció ninguna columna del archivo. '
                    . 'Encabezados encontrados: ' . implode(' | ', $headersDetectados)
                    . '. Separador detectado: "' . ($delimiter === "\t" ? 'tabulación' : $delimiter) . '".',
            ]);
        }

        $parsedRows = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $allEmpty = true;
            $record = [];

            foreach ($columnIndexToDbField as $i => $dbColumn) {
                $value = trim((string) ($row[$i] ?? ''));
                $record[$dbColumn] = $value;
                if ($value !== '') {
                    $allEmpty = false;
                }
            }

            if ($allEmpty) {
                continue;
            }

            $parsedRows[] = $record;
        }

        fclose($handle);

        if (count($parsedRows) === 0) {
            return $this->response->setJSON([
                'message'  => 'El archivo no contenía filas con datos.',
                'inserted' => 0,
                'updated'  => 0,
                'total'    => $this->contactoModel->countAllResults(),
            ]);
        }

        $documentos        = array_values(array_unique(array_filter(array_column($parsedRows, 'nro_documento'))));
        $correosPersonales = array_values(array_unique(array_filter(array_column($parsedRows, 'correo_electronico'))));
        $correosCorp       = array_values(array_unique(array_filter(array_column($parsedRows, 'correo_corporativo'))));

        $existingQuery = $this->contactoModel->select('id, nro_documento, correo_electronico, correo_corporativo');
        $hasCondition = false;

        if (!empty($documentos)) {
            $existingQuery->whereIn('nro_documento', $documentos);
            $hasCondition = true;
        }

        if (!empty($correosPersonales)) {
            $hasCondition
                ? $existingQuery->orWhereIn('correo_electronico', $correosPersonales)
                : $existingQuery->whereIn('correo_electronico', $correosPersonales);
            $hasCondition = true;
        }

        if (!empty($correosCorp)) {
            $hasCondition
                ? $existingQuery->orWhereIn('correo_corporativo', $correosCorp)
                : $existingQuery->whereIn('correo_corporativo', $correosCorp);
            $hasCondition = true;
        }

        $existing = $hasCondition ? $existingQuery->findAll() : [];

        $byDocumento = [];
        $byCorreoPersonal = [];
        $byCorreoCorp = [];

        foreach ($existing as $row) {
            if (!empty($row['nro_documento']) && !isset($byDocumento[$row['nro_documento']])) {
                $byDocumento[$row['nro_documento']] = $row['id'];
            }
            if (!empty($row['correo_electronico']) && !isset($byCorreoPersonal[$row['correo_electronico']])) {
                $byCorreoPersonal[$row['correo_electronico']] = $row['id'];
            }
            if (!empty($row['correo_corporativo']) && !isset($byCorreoCorp[$row['correo_corporativo']])) {
                $byCorreoCorp[$row['correo_corporativo']] = $row['id'];
            }
        }

        $toInsert = [];
        $toUpdate = [];
        $insertKeyIndex = [];

        foreach ($parsedRows as $record) {
            $matchedId = null;

            if (!empty($record['nro_documento']) && isset($byDocumento[$record['nro_documento']])) {
                $matchedId = $byDocumento[$record['nro_documento']];
            } elseif (!empty($record['correo_electronico']) && isset($byCorreoPersonal[$record['correo_electronico']])) {
                $matchedId = $byCorreoPersonal[$record['correo_electronico']];
            } elseif (!empty($record['correo_corporativo']) && isset($byCorreoCorp[$record['correo_corporativo']])) {
                $matchedId = $byCorreoCorp[$record['correo_corporativo']];
            }

            if ($matchedId !== null) {
                $record['id'] = $matchedId;
                $toUpdate[] = $record;
                continue;
            }

            $key = $record['nro_documento'] ?: ($record['correo_electronico'] ?: $record['correo_corporativo']);

            if ($key !== '' && isset($insertKeyIndex[$key])) {
                $toInsert[$insertKeyIndex[$key]] = array_merge($toInsert[$insertKeyIndex[$key]], $record);
                continue;
            }

            $toInsert[] = $record;
            if ($key !== '') {
                $insertKeyIndex[$key] = count($toInsert) - 1;
            }
        }

        $db = db_connect();
        $db->transStart();

        $insertedCount = 0;
        foreach (array_chunk($toInsert, 500) as $chunk) {
            $this->contactoModel->insertBatch($chunk);
            $insertedCount += count($chunk);
        }

        $updatedCount = 0;
        foreach (array_chunk($toUpdate, 500) as $chunk) {
            $this->contactoModel->updateBatch($chunk, 'id');
            $updatedCount += count($chunk);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Ocurrió un error al guardar los datos. No se aplicó ningún cambio.',
            ]);
        }

        $totalEnBD = $this->contactoModel->countAllResults();

        return $this->response->setJSON([
            'message' => "Carga completa. {$insertedCount} contactos nuevos, {$updatedCount} actualizados (ya existían). "
                . "Total de contactos en la base de datos: {$totalEnBD}.",
            'inserted' => $insertedCount,
            'updated'  => $updatedCount,
            'total'    => $totalEnBD,
        ]);
    }

    public function actualizar($id = null)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'ID de contacto inválido']);
        }

        $contacto = $this->contactoModel->find($id);
        if (!$contacto) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Contacto no encontrado']);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'No hay datos para actualizar']);
        }

        $updates = array_intersect_key($data, array_flip($this->contactoModel->allowedFields));

        foreach ($updates as $key => $value) {
            $updates[$key] = ($value === '') ? null : $value;
        }

        if (empty($updates)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'No hay datos para actualizar']);
        }

        $this->contactoModel->update($id, $updates);

        return $this->response->setJSON($this->contactoModel->find($id));
    }

    public function eliminar()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $ids = $data['ids'] ?? [];

        if (!is_array($ids) || count($ids) === 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'No se proporcionaron IDs válidos para eliminar']);
        }

        $validIds = array_values(array_filter(array_map('intval', $ids), fn ($v) => $v > 0));

        if (count($validIds) === 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'No se proporcionaron IDs válidos para eliminar']);
        }

        $this->contactoModel->whereIn('id', $validIds)->delete();

        return $this->response->setJSON([
            'message' => count($validIds) . ' contacto(s) eliminado(s) exitosamente',
            'deletedIds' => $validIds,
        ]);
    }

    public function total()
{
    return $this->response->setJSON([
        'total' => $this->contactoModel->countAllResults(),
    ]);
}

    /**
     * Listado paginado de TODA la data (o de la data filtrada/segmentada).
     * Usado por el explorador de datos con paginación.
     */
    public function listar()
    {
        $page    = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('perPage');
        $perPage = in_array($perPage, [25, 50, 100, 200, 500], true) ? $perPage : 50;

        $filtros = $this->leerFiltrosDeGet();

        $resultado = $this->contactoModel->paginar($page, $perPage, $filtros);

        return $this->response->setJSON($resultado);
    }

    /**
     * Devuelve el mapa de columnas disponibles (para el selector de columnas
     * y el constructor de reglas de segmentación en el frontend).
     */
    public function columnas()
    {
        return $this->response->setJSON(ContactoModel::allColumns());
    }

    /**
     * Cuenta cuántos contactos coinciden con un set de filtros/reglas,
     * sin traer los datos. Usado por la vista previa de segmentación.
     */
    public function contarFiltro()
    {
        $filtros = $this->leerFiltrosDeGet();
        $total = (new ContactoModel())->contarConFiltros($filtros);

        return $this->response->setJSON(['total' => $total]);
    }

    /**
     * Crea un nuevo contacto manualmente.
     */
    public function crear()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'No se recibieron datos para crear el contacto.']);
        }

        $insert = array_intersect_key($data, array_flip($this->contactoModel->allowedFields));

        foreach ($insert as $key => $value) {
            $insert[$key] = ($value === '') ? null : $value;
        }

        $tieneDatos = array_filter($insert, fn ($v) => $v !== null && $v !== '');

        if (empty($tieneDatos)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Debes completar al menos un campo.']);
        }

        $id = $this->contactoModel->insert($insert, true);

        return $this->response->setJSON($this->contactoModel->find($id));
    }

    /**
     * Elimina en bloque todos los contactos que coincidan con un filtro/segmento.
     * Requiere confirmación explícita escribiendo "ELIMINAR" para evitar accidentes.
     */
    public function eliminarPorFiltro()
    {
        ini_set('max_execution_time', 300);

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $filtros = $data['filtros'] ?? [];
        $confirmacion = trim((string) ($data['confirmacion'] ?? ''));

        if ($confirmacion !== 'ELIMINAR') {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Debes escribir ELIMINAR para confirmar esta acción.',
            ]);
        }

        $total = (new ContactoModel())->contarConFiltros($filtros);

        if ($total === 0) {
            return $this->response->setJSON(['message' => 'No hay contactos que coincidan con el filtro.', 'deleted' => 0]);
        }

        if ($total > 20000) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => "La operación afecta a {$total} contactos, demasiados para eliminar de una vez (máx. 20,000). Afina el filtro/segmento.",
            ]);
        }

        $db = db_connect();
        $db->transStart();

        $deleted = 0;
        while (true) {
            $ids = array_column(
                (new ContactoModel())->select('id')->aplicarFiltros($filtros)->findAll(1000),
                'id'
            );

            if (empty($ids)) {
                break;
            }

            $this->contactoModel->whereIn('id', $ids)->delete();
            $deleted += count($ids);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Ocurrió un error al eliminar. No se aplicó ningún cambio.']);
        }

        return $this->response->setJSON([
            'message' => "{$deleted} contacto(s) eliminado(s) exitosamente.",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Exporta a CSV (server-side, no limitado por el navegador) todos los
     * contactos que coincidan con el filtro/segmento actual.
     */
    public function exportarCsv()
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $filtros = $this->leerFiltrosDeGet();

        $total = (new ContactoModel())->contarConFiltros($filtros);

        if ($total === 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'No hay contactos que coincidan con los filtros para exportar.']);
        }

        if ($total > 50000) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => "La exportación afecta a {$total} contactos, demasiados para exportar de una vez (máx. 50,000). Afina el filtro/segmento.",
            ]);
        }

        $dbToLabel = ContactoModel::allColumns(); // dbField => Label
        $fields = array_keys($dbToLabel);

        $rows = (new ContactoModel())
            ->select(implode(',', $fields))
            ->aplicarFiltros($filtros)
            ->findAll($total);

        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, array_values($dbToLabel));

        foreach ($rows as $row) {
            $line = [];
            foreach ($fields as $f) {
                $line[] = $row[$f] ?? '';
            }
            fputcsv($handle, $line);
        }

        rewind($handle);
        $csv = "\xEF\xBB\xBF" . stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="contactos_segmento_' . date('Ymd_His') . '.csv"')
            ->setBody($csv);
    }

    /**
     * Lee de la querystring los filtros comunes (term, pais, empresa, reglas JSON).
     */
    private function leerFiltrosDeGet(): array
    {
        $filtros = [
            'term'    => trim((string) $this->request->getGet('term')),
            'pais'    => trim((string) $this->request->getGet('pais')),
            'empresa' => trim((string) $this->request->getGet('empresa')),
        ];

        $reglasJson = $this->request->getGet('reglas');
        if ($reglasJson) {
            $reglas = json_decode($reglasJson, true);
            if (is_array($reglas)) {
                $filtros['reglas'] = $reglas;
            }
        }

        return $filtros;
    }
}