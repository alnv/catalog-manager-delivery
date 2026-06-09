<?php

use Contao\ArrayUtil;

ArrayUtil::arrayInsert($GLOBALS['BE_MOD']['catalog-manager-extensions'], 1, [
    'delivery' => [
        'name' => 'delivery',
        'tables' => [
            'tl_deliveries'
        ]
    ]
]);