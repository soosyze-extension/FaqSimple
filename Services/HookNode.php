<?php

namespace SoosyzeExtension\FaqSimple\Services;

class HookNode
{
    public function hookNodeEntityFaqShow(&$entity)
    {
        $entity->pathOverride(dirname(__DIR__) . '/Views/');
    }
}
