<?php

namespace CatalogManager\DeliveryBundle\DataContainer;

use CatalogManager\CatalogFieldBuilder as CatalogFieldBuilder;
use CatalogManager\Toolkit as Toolkit;

class Deliveries {


    public function getTables() {

        $arrReturn = [];
        $objDatabase = \Database::getInstance();
        $objCatalog = $objDatabase->prepare('SELECT * FROM tl_catalog ORDER BY name')->execute();

        if ( !$objCatalog->numRows ) {

            return $arrReturn;
        }

        while ( $objCatalog->next() ) {

            $arrReturn[ $objCatalog->tablename ] = $objCatalog->name;
        }

        return $arrReturn;
    }


    public function getTemplates() {

        return \Controller::getTemplateGroup( 'delivery_' );
    }


    public function getTable( \DataContainer $dc ) {

        return $dc->activeRecord->table ? $dc->activeRecord->table : '';
    }


    public function getFields( \DataContainer $objDataContainer = null, $strTable ) {

        $arrReturn = [];
        $objDatabase = \Database::getInstance();
        $arrForbiddenTypes = [ 'upload', 'textarea' ];

        if ( !$strTable ) return $arrReturn;

        $objCatalogFieldBuilder = new CatalogFieldBuilder();
        $objCatalogFieldBuilder->initialize( $strTable );
        $arrFields = $objCatalogFieldBuilder->getCatalogFields( true, null );

        foreach ( $arrFields as $strFieldname => $arrField ) {

            if ( !$objDatabase->fieldExists( $strFieldname, $strTable ) ) continue;
            if ( in_array( $arrField['type'], Toolkit::excludeFromDc() ) ) continue;
            if ( in_array( $arrField['type'], $arrForbiddenTypes ) ) continue;

            $arrReturn[ $strFieldname ] = $arrField['_dcFormat'];
        }

        return $arrReturn;
    }


    public function getSortableFields( $objWidget ) {

        $arrReturn = [];
        $objDatabase = \Database::getInstance();
        $objModule = $objDatabase->prepare( sprintf( 'SELECT * FROM %s WHERE id = ?', $objWidget->strTable ) )->limit(1)->execute( $objWidget->currentRecord );
        $arrFields = $this->getFields( null, $objModule->table );

        if ( is_array( $arrFields ) && !empty( $arrFields ) ) {

            foreach ( $arrFields as $strFieldname => $arrField ) {

                $arrReturn[ $strFieldname ] = isset( $arrField['label'][0] ) ? $arrField['label'][0] : $strFieldname;
            }
        }

        return $arrReturn;
    }


    public function getOrderItems() {

        return [ 'ASC' => &$GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['asc'], 'DESC' => &$GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['desc'] ];
    }
}