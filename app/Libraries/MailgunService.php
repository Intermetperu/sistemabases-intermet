<?php

namespace App\Libraries;

use Config\Mailgun as MailgunConfig;

/**
 * Envío de correos vía API de Mailgun (POST /v3/{domain}/messages, multipart/form-data).
 * Usado por el módulo de "Enviar Correos" de Contactos.
 */
class MailgunService
{
    protected MailgunConfig $config;

    public function __construct()
    {
        $this->config = config('Mailgun');
    }

    /**
     * Envía un correo individual, con imagen embebida por URL (no inline/cid,
     * se referencia directamente la imagen ya subida a /public/assets/correos)
     * y PDF adjunto opcionales.
     *
     * @return array{ok: bool, error?: string}
     */
    public function enviar(string $destinatario, string $asunto, string $html, ?string $pdfAbsolutePath = null): array
    {
        if ($this->config->dominio === '' || $this->config->apiKey === '') {
            return [
                'ok'    => false,
                'error' => 'Mailgun no está configurado. Completa app/Config/Mailgun.php (dominio, apiKey y from).',
            ];
        }

        $url = rtrim($this->config->baseUrl, '/') . '/' . $this->config->dominio . '/messages';

        $fields = [
            'from'    => $this->config->from,
            'to'      => $destinatario,
            'subject' => $asunto,
            'html'    => $html,
        ];

        if ($pdfAbsolutePath && is_file($pdfAbsolutePath)) {
            $fields['attachment'] = new \CURLFile($pdfAbsolutePath, 'application/pdf', basename($pdfAbsolutePath));
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_USERPWD        => 'api:' . $this->config->apiKey,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $fields,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $respuesta = curl_exec($ch);
        $codigo    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorCurl = curl_error($ch);
        curl_close($ch);

        if ($errorCurl) {
            return ['ok' => false, 'error' => $errorCurl];
        }

        if ($codigo >= 200 && $codigo < 300) {
            return ['ok' => true];
        }

        return ['ok' => false, 'error' => "HTTP {$codigo}: {$respuesta}"];
    }
}
