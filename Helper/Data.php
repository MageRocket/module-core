<?php
/**
 * @author MageRocket
 * @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 * @link https://magerocket.com/
 */

namespace MageRocket\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;

class Data extends AbstractHelper
{
    // MAGEROCKET CORE BASE PATH
    protected const MAGEROCKET_XML_CONFIG_PATH = 'magerocket/%s/%s';

    /**
     * @var ModuleListInterface $moduleList
     */
    protected ModuleListInterface $moduleList;

    /**
     * @param Context $context
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList
    )
    {
        parent::__construct($context);
        $this->moduleList = $moduleList;
    }

    /**
     * isMenuEnabled
     * @return bool
     */
    public function isMenuEnabled(): bool
    {
        $configPath = $this->getConfigPath('menu');
        return $this->scopeConfig->isSetFlag($configPath);
    }

    /**
     * getMageRocketModuleList
     * @return array
     */
    public function getMageRocketModuleList()
    {
        $modulesMageRocket = [];
        $moduleList = $this->moduleList->getNames();
        foreach ($moduleList as $name) {
            if (strpos($name, 'MageRocket_') === false) {
                continue;
            }
            $modulesMageRocket[] = $name;
        }
        return $modulesMageRocket;
    }

    /**
     * getConfigPath
     *
     * Return Config Path
     * @param string $field
     * @param string $group
     * @return string
     */
    private function getConfigPath(string $field, string $group = 'general'): string
    {
        return vsprintf(self::MAGEROCKET_XML_CONFIG_PATH, [$group, $field]);
    }
}
