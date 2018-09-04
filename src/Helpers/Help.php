<?php

namespace CatalogManager\DeliveryBundle\Helpers;

use CatalogManager\CatalogFieldBuilder as CatalogFieldBuilder;
use CatalogManager\Toolkit as Toolkit;


class Help {


    public static function parseDelivery( $arrDelivery ) {

        $arrMatch = \StringUtil::deserialize( $arrDelivery['match'], true );
        $arrQuery =  Toolkit::parseQueries( $arrMatch['query'] );

        $arrReturn = [

            'match' => $arrQuery,
            'name' => $arrDelivery['name'],
            'type' => $arrDelivery['return'],
            'table' => $arrDelivery['table'],
            'template' => $arrDelivery['template'],
            'order' => \StringUtil::deserialize( $arrDelivery['order'], true ),
            'globals' => \StringUtil::deserialize( $arrDelivery['globals'], true )
        ];

        $objCatalogFieldBuilder = new CatalogFieldBuilder();
        $objCatalogFieldBuilder->initialize( $arrReturn['table'] );
        $arrFields = $objCatalogFieldBuilder->getCatalogFields( true, null );

        $arrReturn['catalog'] = $objCatalogFieldBuilder->getCatalog();
        $arrReturn['fields'] = $arrFields;

        return $arrReturn;
    }
}