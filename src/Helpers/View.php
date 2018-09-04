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
        $objEntities = $objSQLBuilder->execute( $this->arrQuery );

        if ( !$objEntities-numRows ) {

            return $arrReturn;
        }

        while ( $objEntities->next() ) {

            $arrEntity = $objEntities->row();
            $arrEntity = Toolkit::parseCatalogValues( $arrEntity, $this->arrFields );

            $arrReturn[] = $arrEntity;
        }

        // var_dump($arrReturn);
        // exit;
    }
}