<?php

namespace CatalogManager\DeliveryBundle\Helpers;


use CatalogManager\SQLQueryBuilder as SQLQueryBuilder;
use CatalogManager\Toolkit as Toolkit;


class View {


    protected $arrFields = [];
    protected $arrQuery = [];


    public function __construct( $arrDelivery ) {

        $this->arrFields = $arrDelivery['fields'];

        $this->arrQuery = [

            'table' => $arrDelivery['table'],
            'orderBy' => [],
            'where' => []
        ];

        if ( is_array( $arrDelivery['match'] ) && !empty( $arrDelivery['match'] ) ) {

            $this->arrQuery['where'] = $arrDelivery['match'];
        }

        if ( in_array( 'invisible', $arrDelivery['catalog']['operations'] ) ) {

            $dteTime = \Date::floorToMinute();

            $this->arrQuery['where'][] = [

                'field' => 'tstamp',
                'operator' => 'gt',
                'value' => 0
            ];

            $this->arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'start',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'start',
                    'operator' => 'lte',
                    'value' => $dteTime
                ]
            ];

            $this->arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'stop',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'stop',
                    'operator' => 'gt',
                    'value' => $dteTime
                ]
            ];

            $this->arrQuery['where'][] = [

                'field' => 'invisible',
                'operator' => 'not',
                'value' => '1'
            ];
        }

        if ( is_array( $arrDelivery['order'] ) && !empty( $arrDelivery['order'] ) ) {

            foreach ( $arrDelivery['order'] as $arrOrder ) {

                $this->arrQuery['orderBy'][] = [

                    'field' => $arrOrder['key'],
                    'order' => $arrOrder['value']
                ];
            }
        }
    }


    public function getView() {

        $arrReturn = [];
        $objSQLBuilder = new SQLQueryBuilder();
        $objTotal = $objSQLBuilder->execute( $this->arrQuery );

        $intTotal = $objTotal->numRows;
        $intLimit = $intTotal;
        $intPerPage = 5;
        $intOffset = 0;

        if ( $intPerPage > 0 ) {

            $intPage = ( \Input::get( '_page' ) !== null ) ? \Input::get( _page ) : 1;

            if ( $intPage < 1 || $intPage > max( ceil( $intTotal / $intPerPage ), 1 ) ) {

                throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
            }

            $intOffset = ( $intPage - 1 ) * $intPerPage;
            $intLimit = min( $intPerPage + $intOffset, $intTotal );
        }

        $this->arrQuery['pagination'] = [

            'limit' => $intPerPage,
            'offset' => $intOffset
        ];

        $objEntities = $objSQLBuilder->execute( $this->arrQuery );

        if ( !$objEntities->numRows ) {

            return $arrReturn;
        }

        while ( $objEntities->next() ) {

            $arrEntity = $objEntities->row();
            $arrEntity = Toolkit::parseCatalogValues( $arrEntity, $this->arrFields );

            $arrReturn[] = $arrEntity;
        }

        return $arrReturn;
    }


    public function getPagination() {

        $objSQLBuilder = new SQLQueryBuilder();
        $objTotal = $objSQLBuilder->execute( $this->arrQuery );

        $intTotal = $objTotal->numRows;
        $intLimit = $intTotal;
        $intPerPage = 5;
        $intOffset = 0;

        if ( $intPerPage > 0 ) {

            $objTemplate = new \FrontendTemplate( 'pagination_delivery' );
            $intPage = ( \Input::get( '_page' ) !== null ) ? \Input::get( _page ) : 1;
            $intOffset = ( $intPage - 1 ) * $intPerPage;
            $intLimit = min( $intPerPage + $intOffset, $intTotal );

            $objPagination = new \Pagination( $intTotal, $intPerPage, \Config::get( 'maxPaginationLinks' ), '_page', $objTemplate, true );

            return $objPagination->generate();
        }

        return '';
    }
}