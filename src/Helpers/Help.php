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
            'alias' => $arrDelivery['alias'],
            'target' => $arrDelivery['target'],
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


    public static function getDelivery( $alias ) {

        $objDatabase = \Database::getInstance();
        $objDelivery = $objDatabase->prepare( 'SELECT * FROM tl_deliveries WHERE alias = ? OR id = ?' )->execute( $alias, (int) $alias );

        if ( !$objDelivery->numRows ) {

            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
        }

        return static::parseDelivery( $objDelivery->row() );
    }
}