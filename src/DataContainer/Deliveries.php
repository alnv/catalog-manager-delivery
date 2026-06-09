<?php

namespace CatalogManager\DeliveryBundle\DataContainer;

use Alnv\CatalogManagerBundle\CatalogFieldBuilder as CatalogFieldBuilder;
use Alnv\CatalogManagerBundle\Toolkit as Toolkit;
use Contao\Database;
use Contao\Controller;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\System;

class Deliveries
{
    public function getTables(): array
    {
        $arrReturn = [];
        $objDatabase = Database::getInstance();
        $objCatalog = $objDatabase->prepare('SELECT * FROM tl_catalog ORDER BY name')->execute();

        if (!$objCatalog->numRows) {
            return $arrReturn;
        }

        while ($objCatalog->next()) {
            $arrReturn[$objCatalog->tablename] = $objCatalog->name;
        }

        return $arrReturn;
    }

    public function getTemplates()
    {
        return Controller::getTemplateGroup('delivery_');
    }

    public function getTable(DataContainer $dc)
    {
        return $dc->activeRecord->table ? $dc->activeRecord->table : '';
    }

    public function getFields(DataContainer $objDataContainer = null, $strTable)
    {
        $arrReturn = [];
        $objDatabase = Database::getInstance();
        $arrForbiddenTypes = ['upload', 'textarea'];

        if (!$strTable) return $arrReturn;

        $objCatalogFieldBuilder = new CatalogFieldBuilder();
        $objCatalogFieldBuilder->initialize($strTable);
        $arrFields = $objCatalogFieldBuilder->getCatalogFields(true, null);

        foreach ($arrFields as $strFieldname => $arrField) {

            if (!$objDatabase->fieldExists($strFieldname, $strTable)) continue;
            if (in_array($arrField['type'], Toolkit::excludeFromDc())) continue;
            if (in_array($arrField['type'], $arrForbiddenTypes)) continue;

            $arrReturn[$strFieldname] = $arrField['_dcFormat'];
        }

        return $arrReturn;
    }

    public function getSortableFields($objWidget): array
    {
        $arrReturn = [];
        $objDatabase = Database::getInstance();
        $objModule = $objDatabase->prepare(sprintf('SELECT * FROM %s WHERE id = ?', $objWidget->strTable))->limit(1)->execute($objWidget->currentRecord);
        $arrFields = $this->getFields(null, $objModule->table);

        if (is_array($arrFields) && !empty($arrFields)) {
            foreach ($arrFields as $strFieldname => $arrField) {
                $arrReturn[$strFieldname] = isset($arrField['label'][0]) ? $arrField['label'][0] : $strFieldname;
            }
        }

        return $arrReturn;
    }

    public function getOrderItems(): array
    {
        return ['ASC' => &$GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['asc'], 'DESC' => &$GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['desc']];
    }

    public function generateAlias($strValue, DataContainer $objDataContainer)
    {
        $objDatabase = Database::getInstance();

        if ($strValue == '') {
            $strValue = System::getContainer()->get('contao.slug.generator')->generate(StringUtil::prepareSlug($objDataContainer->activeRecord->name));
        }

        $objAlias = $objDatabase->prepare("SELECT id FROM tl_deliveries WHERE alias = ? AND id != ?")->execute($strValue, $objDataContainer->id);
        if ($objAlias->numRows) {
            $strValue .= '-' . $objDataContainer->id;
        }

        return $strValue;
    }
}