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

        $arrData = [];
        $arrDelivery = Help::getDelivery( $alias );
        $objView = new View( $arrDelivery );

        $arrData['data'] = $objView->getView();
        $arrData['globals'] = $arrDelivery['globals'];

        if ( $arrDelivery['type'] == 'template' ) {

            $strTemplate = $arrDelivery['template'] ? $arrDelivery['template'] : 'delivery_example';
            $objTemplate = new \FrontendTemplate( $strTemplate );
            $objTemplate->setData( $arrData );

            $arrData['template'] = $objTemplate->parse();
        }

        header('Content-Type: application/json');
        echo json_encode( $arrData );
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

        $arrDelivery['alias'] = $alias;
        $arrDelivery['target'] = 'deals-wrapper';
        $arrDelivery['host'] = 'http://catalog-manager-demo.host';

        $objTemplate = new \FrontendTemplate( 'js_delivery' );
        $objTemplate->setData( $arrDelivery );

        header('Content-Type: application/javascript');
        echo $objTemplate->parse();
        exit;
    }
}