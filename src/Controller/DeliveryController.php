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
        $objDatabase = \Database::getInstance();
        $objDelivery = $objDatabase->prepare( 'SELECT * FROM tl_deliveries WHERE alias = ? OR id = ?' )->execute( $alias, (int) $alias );

        if ( !$objDelivery->numRows ) {

            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
        }

        $arrDelivery = Help::parseDelivery( $objDelivery->row() );
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
}