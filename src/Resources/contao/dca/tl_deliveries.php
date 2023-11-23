<?php

$GLOBALS['TL_DCA']['tl_deliveries'] = [
    'config' => [
        'dataContainer' => 'Table',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index'
            ]
        ]
    ],
    'list' => [
        'sorting' => [
            'mode' => 0,
        ],
        'label' => [
            'showColumns' => true,
            'fields' => ['name', 'alias', 'table', 'return']
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'header.svg'
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg'
            ]
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ]
    ],

    'palettes' => [
        '__selector__' => ['return'],
        'default' => '{general_settings},name,alias,target;{data_settings},table,perPage,match,order;{return_settings},return;{expert_settings},globals',
    ],
    'subpalettes' => [
        'return_template' => 'template'
    ],

    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],

        'name' => [
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50',
                'maxlength' => 128
            ],
            'exclude' => true,
            'sql' => "varchar(128) NOT NULL default ''"
        ],
        'alias' => [
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'folderalias',
                'tl_class' => 'w50',
                'doNotCopy' => true,
                'maxlength' => 32
            ],
            'save_callback' => [
                ['delivery.datacontainer.deliveries', 'generateAlias']
            ],
            'exclude' => true,
            'sql' => "varchar(32) NOT NULL default ''"
        ],
        'target' => [
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w50',
                'mandatory' => true,
                'maxlength' => 32
            ],
            'exclude' => true,
            'sql' => "varchar(32) NOT NULL default ''"
        ],
        'perPage' => [
            'inputType' => 'text',
            'default' => 0,
            'eval' => [
                'minval' => 0,
                'maxval' => 100,
                'tl_class' => 'w50',
                'mandatory' => true
            ],
            'exclude' => true,
            'sql' => "smallint(5) unsigned NOT NULL default '0'"
        ],
        'table' => [
            'inputType' => 'select',
            'eval' => [
                'chosen' => true,
                'maxlength' => 64,
                'tl_class' => 'w50',
                'mandatory' => true,
                'submitOnChange' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],
            'options_callback' => ['delivery.datacontainer.deliveries', 'getTables'],
            'exclude' => true,
            'sql' => "varchar(64) NOT NULL default ''"
        ],
        'match' => [
            'inputType' => 'catalogTaxonomyWizard',
            'eval' => [
                'tl_class' => 'clr',
                'dcTable' => 'tl_deliveries',
                'taxonomyTable' => ['CatalogManager\DeliveryBundle\DataContainer\Deliveries', 'getTable'],
                'taxonomyEntities' => ['CatalogManager\DeliveryBundle\DataContainer\Deliveries', 'getFields']
            ],
            'exclude' => true,
            'sql' => "blob NULL"
        ],
        'order' => [
            'inputType' => 'catalogDuplexSelectWizard',
            'eval' => [
                'chosen' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true,
                'mainLabel' => 'catalogManagerFields',
                'dependedLabel' => 'catalogManagerOrder',
                'mainOptions' => ['CatalogManager\DeliveryBundle\DataContainer\Deliveries', 'getSortableFields'],
                'dependedOptions' => ['CatalogManager\DeliveryBundle\DataContainer\Deliveries', 'getOrderItems']
            ],
            'exclude' => true,
            'sql' => "blob NULL"
        ],
        'globals' => [
            'inputType' => 'keyValueWizard',
            'eval' => [
                'tl_class' => 'clr',
            ],
            'exclude' => true,
            'sql' => "blob NULL"
        ],
        'return' => [
            'inputType' => 'radio',
            'default' => 'json',
            'eval' => [
                'maxlength' => 16,
                'tl_class' => 'clr',
                'submitOnChange' => true,
                'blankOptionLabel' => '-',
                'includeBlankOption' => true,
            ],
            'options' => [
                'json',
                'template'
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_deliveries']['reference']['return'],
            'exclude' => true,
            'sql' => "varchar(16) NOT NULL default ''"
        ],
        'template' => [
            'inputType' => 'select',
            'default' => 'delivery_example',
            'eval' => [
                'chosen' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
                'blankOptionLabel' => '-',
                'includeBlankOption' => true
            ],
            'options_callback' => ['delivery.datacontainer.deliveries', 'getTemplates'],
            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ]
    ]
];