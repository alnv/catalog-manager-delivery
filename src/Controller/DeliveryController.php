<?php

namespace CatalogManager\DeliveryBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use CatalogManager\DeliveryBundle\Helpers\Help as Help;
use CatalogManager\DeliveryBundle\Helpers\View as View;

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

        global $objPage;

        if ( $objPage == null ) {

            $objPage = new \stdClass();
            $objPage->language = $GLOBALS['TL_LANGUAGE'];
        }

        define( 'TL_ASSETS_URL', '' );
        define( 'TL_FILES_URL', '' );

        $arrData = [];
        $arrDelivery = Help::getDelivery( $alias );
        $objView = new View( $arrDelivery );

        $arrData['pagination'] = $objView->getPagination();
        $arrData['globals'] = $arrDelivery['globals'];
        $arrData['data'] = $objView->getView();

        if ( $arrDelivery['type'] == 'template' ) {

            $strTemplate = $arrDelivery['template'] ? $arrDelivery['template'] : 'delivery_example';
            $objTemplate = new \FrontendTemplate( $strTemplate );
            $objTemplate->setData( $arrData );

            $arrData['template'] = $objTemplate->parse();
        }

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        echo json_encode( $arrData, 512 );
        exit;
    }

    /**
     *
     * @Route("/count/{alias}", name="count")
     * @Method({"GET"})
     */
    public function count( $alias ) {

        $this->container->get( 'contao.framework' )->initialize();

        $arrDelivery = Help::getDelivery( $alias );
        $objView = new View( $arrDelivery );

        $arrData = [

            'count' => $objView->getCount()
        ];

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        echo json_encode( $arrData, 512 );
        exit;
    }

    /**
     *
     * @Route("/js/{alias}.js", name="javascript")
     * @Method({"GET"})
     */
    public function javascript( $alias ) {

        $this->container->get( 'contao.framework' )->initialize();

        $arrDelivery = Help::getDelivery( $alias );
        $objTemplate = new \FrontendTemplate( 'js_delivery' );
        $objTemplate->setData( $arrDelivery );

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/javascript');

        echo $objTemplate->parse();
        exit;
    }
}