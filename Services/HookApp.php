<?php

namespace SoosyzeExtension\FaqSimple\Services;

class HookApp
{
    /**
     * @var \Soosyze\App
     */
    protected $core;

    public function __construct($core)
    {
        $this->core = $core;
    }

    public function hookResponseAfter($request, &$response)
    {
        if (!($response instanceof \SoosyzeCore\Template\Services\Templating)) {
            return;
        }
        $assets = $this->core->getPath('modules_contributed', 'app/modules', false) . '/FaqSimple/Assets/';
        $script = $response->getBlock('this')->getVar('scripts');
        $script .= '<script src="' . $assets . 'script.js"></script>';

        $response->view('this', [ 'scripts' => $script ]);
    }
}
