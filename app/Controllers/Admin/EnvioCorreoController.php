<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContactoModel;
use App\Models\EnvioCorreoLogModel;
use App\Libraries\MailgunService;

class EnvioCorreoController extends BaseController
{
    protected ContactoModel $contactoModel;
    protected EnvioCorreoLogModel $logModel;

    /** Límite de correos enviados en una ventana móvil de 24 horas. */
    private const LIMITE_24H = 50;

    public function __construct()
    {
        $this->contactoModel = new ContactoModel();
        $this->logModel = new EnvioCorreoLogModel();
    }

    /**
     * Cuánto cupo queda en las últimas 24 horas.
     */
    public function cuota()
    {
        $enviados = $this->logModel->contarEnviadosUltimas24h();

        return $this->response->setJSON([
            'limite'            => self::LIMITE_24H,
            'enviados'          => $enviados,
            'disponibles'       => max(0, self::LIMITE_24H - $enviados),
            'proximaLiberacion' => $this->logModel->proximaLiberacion(),
        ]);
    }

    /**
     * Sube la imagen (banner) o el PDF que se usará en la plantilla del correo.
     * Se guarda en public/assets/correos para poder referenciarse por URL directa.
     */
    public function subirArchivo()
    {
        $tipo = (string) $this->request->getPost('tipo'); // 'imagen' | 'pdf'
        $file = $this->request->getFile('archivo');

        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Archivo inválido.']);
        }

        $mime = $file->getClientMimeType();
        $esImagenValida = in_array($mime, ['image/png', 'image/jpeg', 'image/jpg'], true);
        $esPdfValido = $mime === 'application/pdf';

        if ($tipo === 'imagen' && !$esImagenValida) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Sube una imagen en formato JPG o PNG.']);
        }

        if ($tipo === 'pdf' && !$esPdfValido) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Sube un archivo en formato PDF.']);
        }

        if ($tipo !== 'imagen' && $tipo !== 'pdf') {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tipo de archivo no reconocido.']);
        }

        $carpeta = FCPATH . 'assets/correos/';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $nombre = $file->getRandomName();
        $file->move($carpeta, $nombre);

        return $this->response->setJSON([
            'path' => 'assets/correos/' . $nombre,
            'url'  => base_url('assets/correos/' . $nombre),
        ]);
    }

    /**
     * Envía la plantilla de correo a los contactos seleccionados, respetando
     * el límite de LIMITE_24H correos por ventana móvil de 24 horas.
     */
    public function enviar()
    {
        ini_set('max_execution_time', 300);

        $data = $this->request->getJSON(true) ?? [];

        $asunto     = trim((string) ($data['asunto'] ?? ''));
        $cuerpoHtml = (string) ($data['cuerpoHtml'] ?? '');
        $imagenPath = trim((string) ($data['imagenPath'] ?? ''));
        $pdfPath    = trim((string) ($data['pdfPath'] ?? ''));
        $ids        = $data['contactoIds'] ?? [];

        if ($asunto === '') {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'El asunto es obligatorio.']);
        }

        if ($cuerpoHtml === '' && $imagenPath === '') {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Agrega al menos un cuerpo de correo o una imagen.']);
        }

        if (!is_array($ids) || count($ids) === 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Selecciona al menos un contacto.']);
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn ($v) => $v > 0)));

        $enviadosUltimas24h = $this->logModel->contarEnviadosUltimas24h();
        $disponibles = max(0, self::LIMITE_24H - $enviadosUltimas24h);

        if ($disponibles === 0) {
            return $this->response->setStatusCode(429)->setJSON([
                'message' => 'Alcanzaste el límite de ' . self::LIMITE_24H . ' correos en las últimas 24 horas. Intenta más tarde.',
                'proximaLiberacion' => $this->logModel->proximaLiberacion(),
            ]);
        }

        $pendientesPorCuota = 0;
        if (count($ids) > $disponibles) {
            $pendientesPorCuota = count($ids) - $disponibles;
            $ids = array_slice($ids, 0, $disponibles);
        }

        $contactos = $this->contactoModel
            ->select('id, nombres_y_apellidos_completos, empresa, correo_corporativo, correo_electronico')
            ->whereIn('id', $ids)
            ->findAll();

        $mailgun = new MailgunService();

        $rutaPdfAbsoluta = $pdfPath !== '' ? FCPATH . $pdfPath : null;
        $urlImagen = $imagenPath !== '' ? base_url($imagenPath) : null;

        $enviados = 0;
        $fallidos = 0;
        $sinCorreo = 0;
        $detalles = [];

        foreach ($contactos as $c) {
            $destino = trim((string) ($c['correo_corporativo'] ?? '')) ?: trim((string) ($c['correo_electronico'] ?? ''));

            if (!$destino) {
                $sinCorreo++;
                $detalles[] = ['id' => $c['id'], 'estado' => 'sin_correo', 'nombre' => $c['nombres_y_apellidos_completos'] ?: null];
                continue;
            }

            $nombre  = $c['nombres_y_apellidos_completos'] ?: '';
            $empresa = $c['empresa'] ?: '';

            $asuntoFinal = str_replace(['{{nombre}}', '{{empresa}}'], [$nombre, $empresa], $asunto);
            $cuerpoFinal = str_replace(['{{nombre}}', '{{empresa}}'], [$nombre, $empresa], $cuerpoHtml);

            if ($urlImagen) {
                $cuerpoFinal = '<img src="' . $urlImagen . '" style="max-width:100%; height:auto;" alt=""><br><br>' . $cuerpoFinal;
            }

            $resultado = $mailgun->enviar($destino, $asuntoFinal, $cuerpoFinal, $rutaPdfAbsoluta);
            $estado = $resultado['ok'] ? 'enviado' : 'error';

            $this->logModel->insert([
                'contacto_id'    => $c['id'],
                'correo_destino' => $destino,
                'asunto'         => $asuntoFinal,
                'estado'         => $estado,
                'error_detalle'  => $resultado['ok'] ? null : ($resultado['error'] ?? null),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            $this->contactoModel->update($c['id'], [
                'estado_envio_correo' => $estado,
                'correo_enviado_en'   => date('Y-m-d H:i:s'),
            ]);

            $estado === 'enviado' ? $enviados++ : $fallidos++;

            $detalles[] = [
                'id'      => $c['id'],
                'estado'  => $estado,
                'correo'  => $destino,
                'nombre'  => $nombre ?: null,
                'error'   => $resultado['ok'] ? null : ($resultado['error'] ?? 'Error desconocido'),
            ];

            // Si un solo envío falla por límite de Mailgun a mitad de lote, no seguimos gastando tiempo/cupo.
            if (!$resultado['ok'] && str_contains((string) ($resultado['error'] ?? ''), 'HTTP 429')) {
                break;
            }
        }

        $mensaje = "{$enviados} correo(s) enviado(s), {$fallidos} fallido(s), {$sinCorreo} sin correo registrado.";
        if ($pendientesPorCuota > 0) {
            $mensaje .= " {$pendientesPorCuota} quedaron pendientes por el límite de " . self::LIMITE_24H . '/24h.';
        }

        return $this->response->setJSON([
            'message'            => $mensaje,
            'enviados'           => $enviados,
            'fallidos'           => $fallidos,
            'sinCorreo'          => $sinCorreo,
            'pendientesPorCuota' => $pendientesPorCuota,
            'detalles'           => $detalles,
        ]);
    }
}
