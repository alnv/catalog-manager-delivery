<?php

namespace CatalogManager\DeliveryBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use CatalogManager\DeliveryBundle\Helpers\Help as Help;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 *
 * @Route("/delivery-api", defaults={"_scope" = "frontend", "_token_check" = false})
 */
class DeliveryController extends Controller {


    /**
     *
     * @Route("/{alias}", name="delivery")
     * @Method({"GET"})
     */
    public function delivery( $alias ) {

        $this->container->get( 'contao.framework' )->initialize();

        $arrReturn = [];
        $objDatabase = \Database::getInstance();
        $objDelivery = $objDatabase->prepare( 'SELECT * FROM tl_deliveries WHERE alias = ? OR id = ?' )->execute( $alias, (int) $alias );

        if ( !$objDelivery->numRows ) {

            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
        }

        $arrDelivery = Help::parseDelivery( $objDelivery->row() );

        //

        header('Content-Type: application/json');
        echo json_encode( $arrDelivery );
        exit;
    }
}