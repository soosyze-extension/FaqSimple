<?php

namespace SoosyzeExtension\FaqSimple\Services;

class HookNode
{
    public function hookNodeEntityFaqShow(&$entity)
    {
        $entity->addPathOverride(dirname(__DIR__) . '/Views/');
    }
}
