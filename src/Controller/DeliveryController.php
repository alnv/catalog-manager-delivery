<?php

namespace CatalogManager\DeliveryBundle\Controller;

use Contao\FrontendTemplate;
use CatalogManager\DeliveryBundle\Helpers\Help as Help;
use CatalogManager\DeliveryBundle\Helpers\View as View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Contao\CoreBundle\Controller\AbstractController;

#[Route(path: 'delivery-api', name: 'delivery-api-controller', defaults: ['_scope' => 'frontend', '_token_check' => false])]
class DeliveryController extends AbstractController
{

    #[Route(path: '/{alias}', methods: ["GET"])]
    public function delivery($alias): JsonResponse
    {
        $this->container->get('contao.framework')->initialize();

        global $objPage;

        if ($objPage == null) {
            $objPage = new \stdClass();
            $objPage->language = $GLOBALS['TL_LANGUAGE'] ?? 'de';
        }

        define('TL_ASSETS_URL', '');
        define('TL_FILES_URL', '');

        $arrData = [];
        $arrDelivery = Help::getDelivery($alias);
        $objView = new View($arrDelivery);

        $arrData['pagination'] = $objView->getPagination();
        $arrData['globals'] = $arrDelivery['globals'];
        $arrData['data'] = $objView->getView();

        if ($arrDelivery['type'] == 'template') {
            $strTemplate = $arrDelivery['template'] ?: 'delivery_example';
            $objTemplate = new FrontendTemplate($strTemplate);
            $objTemplate->setData($arrData);

            $arrData['template'] = $objTemplate->parse();
        }

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        return new JsonResponse($arrData);
    }

    #[Route(path: '/count/{alias}', methods: ["GET"])]
    public function count($alias)
    {
        $this->container->get('contao.framework')->initialize();

        $arrDelivery = Help::getDelivery($alias);
        $objView = new View($arrDelivery);

        $arrData = [
            'count' => $objView->getCount()
        ];

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        return new JsonResponse($arrData);
    }

    #[Route(path: '/js/{alias}.js', methods: ["GET"])]
    public function javascript($alias)
    {
        $this->container->get('contao.framework')->initialize();

        $arrDelivery = Help::getDelivery($alias);
        $objTemplate = new FrontendTemplate('js_delivery');
        $objTemplate->setData($arrDelivery);

        header('Access-Control-Allow-Origin: *');

        echo $objTemplate->parse();
        exit;
    }
}