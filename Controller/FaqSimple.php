<?php

namespace SoosyzeExtension\FaqSimple\Controller;

class FaqSimple extends \Soosyze\Controller
{
    public function __construct()
    {
        $this->pathServices = dirname(__DIR__) . '/Config/service.json';
    }
}
