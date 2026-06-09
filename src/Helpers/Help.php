<?php

namespace CatalogManager\DeliveryBundle\Helpers;

use Alnv\CatalogManagerBundle\CatalogFieldBuilder as CatalogFieldBuilder;
use Alnv\CatalogManagerBundle\Toolkit as Toolkit;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\StringUtil;
use Contao\Database;
use Contao\Environment;

class Help
{
    public static function parseDelivery($arrDelivery)
    {
        $arrMatch = StringUtil::deserialize($arrDelivery['match'], true);
        $arrQuery = Toolkit::parseQueries($arrMatch['query']);

        $arrReturn = [
            'match' => $arrQuery,
            'name' => $arrDelivery['name'],
            'type' => $arrDelivery['return'],
            'table' => $arrDelivery['table'],
            'alias' => $arrDelivery['alias'],
            'target' => $arrDelivery['target'],
            'template' => $arrDelivery['template'],
            'perPage' => (int)$arrDelivery['perPage'],
            'order' => StringUtil::deserialize($arrDelivery['order'], true),
            'globals' => StringUtil::deserialize($arrDelivery['globals'], true)
        ];

        $objCatalogFieldBuilder = new CatalogFieldBuilder();
        $objCatalogFieldBuilder->initialize($arrReturn['table']);
        $arrFields = $objCatalogFieldBuilder->getCatalogFields(true, null);

        $arrReturn['catalog'] = $objCatalogFieldBuilder->getCatalog();
        $arrReturn['fields'] = $arrFields;

        return $arrReturn;
    }


    public static function getDelivery($alias)
    {
        $objDatabase = Database::getInstance();
        $objDelivery = $objDatabase->prepare('SELECT * FROM tl_deliveries WHERE alias = ? OR id = ?')->execute($alias, (int)$alias);

        if (!$objDelivery->numRows) {
            throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
        }

        return static::parseDelivery($objDelivery->row());
    }
}