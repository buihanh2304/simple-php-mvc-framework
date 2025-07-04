<?php
declare(strict_types=1);

namespace System\Classes;

use Medoo\Medoo;

class Model
{
    protected Medoo $db;
    protected Config $config;

    public function __construct()
    {
        $this->db = app(Medoo::class);
        $this->config = app(Config::class);
    }
}
