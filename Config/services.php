<?php

return [
    'faqsimple.extend'    => [
        'class' => 'SoosyzeExtension\FaqSimple\Extend',
        'hooks' => [
            'install.user' => 'hookInstallUser'
        ]
    ],
    'faqsimple.node.hook' => [
        'class' => 'SoosyzeExtension\FaqSimple\Hook\Node',
        'hooks' => [
            'node.entity.faq.show' => 'hookNodeEntityFaqShow'
        ]
    ]
];
