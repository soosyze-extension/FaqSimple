<?php

namespace SoosyzeExtension\FaqSimple\Hook;

class Node
{
    public function hookNodeEntityFaqShow(&$entity)
    {
        $entity->addPathOverride(dirname(__DIR__) . '/Views/');
    }
}
