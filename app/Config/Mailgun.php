<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Mailgun extends BaseConfig
{
    public string $dominio = 'mg.encuentrometalurgia.com';
    public string $apiKey  = '';
    public string $from    = 'Encuentro Metalurgia <no-reply@mg.encuentrometalurgia.com>';
    public string $baseUrl = 'https://api.mailgun.net/v3';

    public function __construct()
    {
        parent::__construct();

        $this->dominio = env('mailgun.dominio', $this->dominio);
        $this->apiKey  = env('mailgun.apiKey', $this->apiKey);
        $this->from    = env('mailgun.from', $this->from);
        $this->baseUrl = env('mailgun.baseUrl', $this->baseUrl);
    }
}