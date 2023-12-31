<?php
/**
 *  @author MageRocket
 *  @copyright Copyright (c) 2023 MageRocket (https://magerocket.com/)
 *  @link https://magerocket.com/
 */

namespace MageRocket\Core\Model\Config\Structure;

use Magento\Config\Model\Config\Structure\Data as StructureData;
use MageRocket\Core\Block\Adminhtml\System\Config\Form\Field\Version;
use MageRocket\Core\Helper\Data as Helper;

class Data
{
    /**
     * @var Helper $helper
     */
    protected Helper $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * beforeMerge
     * @param StructureData $object
     * @param array $config
     * @return array
     */
    public function beforeMerge(StructureData $object, array $config)
    {
        if (!isset($config['config']['system'])) {
            return [$config];
        }

        // Get MageRocket Modules
        $modulesMageRocket = $this->helper->getMageRocketModuleList();

        // Check if the module sector exists
        $position = 2;
        foreach ($modulesMageRocket as $module) {
            // Exclude Core
            if('MageRocket_Core' === $module){
                continue;
            }

            // Check Section
            [$vendor, $module] = explode('_', $module);
            $moduleSectionId = 'mr'.strtolower($module);
            $sectionsKeys = array_keys($config['config']['system']['sections']);
            if(!in_array($moduleSectionId, $sectionsKeys)){
                $config['config']['system']['sections'][$moduleSectionId] = $this->addSection($module, $moduleSectionId, $position++);
            }
        }

        /** @var array $sections */
        $sections = $config['config']['system']['sections'];
        foreach ($sections as $sectionId => $section) {
            if (isset($section['tab']) && ($section['tab'] === 'magerocket_extensions') && ($section['id'] !== 'magerocket')) {
                foreach ($modulesMageRocket as $moduleName) {
                    $dynamicGroups = $this->getDynamicConfigGroups($moduleName, $section['id']);
                    if (!empty($dynamicGroups)) {
                        if(isset($config['config']['system']['sections'][$sectionId]['children'])){
                            // Append Group
                            $config['config']['system']['sections'][$sectionId]['children'] = $dynamicGroups + $section['children'];
                        } else {
                            // Add Group
                            $config['config']['system']['sections'][$sectionId]['children'] = $dynamicGroups;
                        }
                    }
                    break;
                }
            }
        }

        return [$config];
    }

    /**
     * @param $moduleName
     * @param $sectionName
     *
     * @return mixed
     */
    protected function getDynamicConfigGroups($moduleName, $sectionName)
    {
        $fieldsetTemplate = [
            'type'          => 'text',
            'showInDefault' => '1',
            'showInWebsite' => '0',
            'showInStore'   => '0',
            'sortOrder'     => 1,
            'module_name'   => $moduleName,
            'validate'      => 'required-entry',
            '_elementType'  => 'field',
            'path'          => $sectionName . '/module',
        ];

        $fields = [];
        foreach ($this->getFieldList() as $id => $option) {
            $fields[$id] = array_merge($fieldsetTemplate, ['id' => $id], $option);
        }

        // Return New Group
        return [
            'module' => [
                'id'            => 'module',
                'label'         => __('Module Information'),
                'showInDefault' => '1',
                'showInWebsite' => '0',
                'showInStore'   => '0',
                '_elementType'  => 'group',
                'path'          => $sectionName,
                'children'      => $fields,
            ],
        ];
    }

    /**
     * Return fields to add
     * @return array
     */
    protected function getFieldList()
    {
        // Init Fields array
        $fields = [];

        // Version Module
        $fields['version'] = [
            'type'           => 'label',
            'label'          => __('Version Installed'),
            'frontend_model' => Version::class,
        ];

        // Return Fields
        return $fields;
    }

    /**
     * addSection
     * @param $label
     * @param $sectionId
     * @param int $position
     * @return array
     */
    private function addSection($label, $sectionId, $position = 3)
    {
        return [
            'id' => $sectionId,
            'translate' => 'label',
            'type' => 'text',
            'sortOrder' => $position,
            'showInDefault' => 1,
            'showInWebsite' => 1,
            'showInStore' => 1,
            'label' => $label,
            'tab' => 'magerocket_extensions',
            'resource' => 'MageRocket_Core::configuration',
            '_elementType' => 'section',
        ];
    }
}
