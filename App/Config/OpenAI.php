<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class OpenAI extends BaseConfig
{
    public $apiKey = env('OPENAI_API_KEY');
    public $topic = env('PARLIAMENT_TOPIC');
}