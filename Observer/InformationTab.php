<?php
/**
 * @author MageRocket
 * @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 * @link https://magerocket.com/
 */

namespace MageRocket\Core\Observer;

use MageRocket\Core\Model\ModuleInfoProvider;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use MageRocket\Core\Model\ExtensionProvider;

class InformationTab implements ObserverInterface
{
    public const SEO_PARAMS = '?utm_source=extension&utm_medium=backend&utm_campaign=%s';
    public const FEATURE_LINK = 'https://magerocket.com/request-a-feature';
    public const MAGEROCKET_BUTTON = '<a href="%s" target="_blank" class="magerocket-button %s"><span>%s</span></a>';

    /**
     * @var AbstractBlock $block
     */
    protected AbstractBlock $block;

    /**
     * @var ModuleInfoProvider $moduleInfoProvider
     */
    protected ModuleInfoProvider $moduleInfoProvider;

    /**
     * @var ExtensionProvider $extensionProvider
     */
    protected ExtensionProvider $extensionProvider;

    /**
     * @param ExtensionProvider $extensionProvider
     * @param ModuleInfoProvider $moduleInfoProvider
     */
    public function __construct(
        ExtensionProvider $extensionProvider,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->extensionProvider = $extensionProvider;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block) {
            $this->setBlock($block);
            $html = $this->generateBlockContent();
            $block->setContent($html);
        }
    }

    /**
     * @return mixed
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param mixed $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }

    /**
     * generateBlockContent
     * @return string
     */
    private function generateBlockContent(): string
    {
        $html = '<div class="magerocket-block-container">';
        $html .= $this->showVersionInfo();
        $html .= $this->getMageRocketActions();
        $html .= '</div>';
        return $html;
    }

    /**
     * showVersionInfo
     * @return string
     */
    private function showVersionInfo(): string
    {
        $html = '<div class="magerocket-module-info">';
        $html .= $this->getModuleInfo();
        $html .= $this->showModuleMode();
        $needUpdate = $this->needUpdate();
        if ($needUpdate) {
            $html .=
                '<span class="upgrade-error message message-warning">'
                . __('Updates are available. We recommend updating as soon as possible')
                . '</span>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * getMageRocketActions
     * @return string
     */
    private function getMageRocketActions(): string
    {
        if (!$this->getBlock()->showButtonFeatureRequest() && !$this->getBlock()->getUserGuide()) {
            return '';
        }
        // Request Button
        $html = '<div class="magerocket-actions">';
        if ($this->getBlock()->showButtonFeatureRequest()) {
            $html .= $this->getFeatureButton();
        }
        // Guide Button
        if ($this->getBlock()->getUserGuide()) {
            $html .= $this->getGuideButton($this->getBlock()->getUserGuide());
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * getChangeLogLink
     * @return string
     */
    private function getChangeLogLink()
    {
        return $this->getLinkSeo($this->getModuleLink() . "#changelog", 'changelog_%s');
    }

    /**
     * getGuideButton
     * @param $guide
     * @return string
     */
    private function getGuideButton($guide): string
    {
        $guideLink = $this->getLinkSeo($guide);
        return vsprintf(self::MAGEROCKET_BUTTON, [$guideLink,'btn-guide',__("User Guide")]);
    }

    /**
     * getFeatureButton
     * @return string
     */
    private function getFeatureButton(): string
    {
        $featureLink = $this->getLinkSeo(self::FEATURE_LINK, 'module_%s');
        return vsprintf(self::MAGEROCKET_BUTTON, [$featureLink,'btn-feature',__("Request New Feature")]);
    }

    /**
     * getLinkSeo
     * @param string $link
     * @param string $campaign
     * @return string
     */
    private function getLinkSeo(string $link, string $campaign = 'user_guide'):string
    {
        if ($link) {
            if (str_contains($campaign, '%s')) {
                $moduleName = $this->getBlock()->getModuleCode();
                $campaign = sprintf($campaign, $moduleName);
            }
            $link .= sprintf(self::SEO_PARAMS, $campaign);
        }
        return $link;
    }

    /**
     * needUpdate
     * @return bool
     */
    protected function needUpdate(): bool
    {
        $data = $this->moduleInfoProvider->getModuleInfo($this->getModuleCode());
        $module = $this->extensionProvider->checkModuleUpdates($data['name']);
        if (isset($module['version']) && version_compare($module['version'], (string)$this->getInstalledVersion(), '>')
        ) {
            return true;
        }
        return false;
    }

    /**
     * getInstalledVersion
     * @return mixed|null
     */
    protected function getInstalledVersion()
    {
        $data = $this->moduleInfoProvider->getModuleInfo($this->getModuleCode());
        return $data['version'] ?? null;
    }

    /**
     * showModuleMode
     * @return string
     */
    protected function showModuleMode()
    {
        $data = $this->moduleInfoProvider->getModuleInfo($this->getModuleCode());
        $moduleMode = $data['extra']['dev'] ?? false;
        $html = "";
        if ($moduleMode) {
            $html .= '<span class="upgrade-error message message-warning">';
            $html .= __('<b>ATTENTION</b>: The installed module corresponds to a development version.');
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * getModuleCode
     * @return string
     */
    private function getModuleCode(): string
    {
        return $this->getBlock()->getModuleCode();
    }

    /**
     * getModulePage
     * @return string
     */
    private function getModulePage(): string
    {
        return $this->getBlock()->getModulePage();
    }

    /**
     * getModuleLink
     * @return string
     */
    private function getModuleLink()
    {
        return $this->block->getModulePage();
    }

    /**
     * @return string
     */
    private function getModuleInfo()
    {
        $moduleInfo = '';
        $moduleName = $this->block->getModuleName();
        $moduleLogo = $this->block->getModuleLogo();
        $moduleDescription = $this->block->getModuleDescription();
        // Set Title
        if ($moduleName && !$moduleLogo) {
            $moduleInfo .= "<h1>$moduleName</h1>";
        }
        // Set Logo
        if ($moduleLogo && !$moduleName) {
            $moduleInfo .= "<img src='$moduleLogo'/>";
        }
        // Set Description
        if ($moduleDescription) {
            $moduleInfo .= "<p>$moduleDescription</p>";
        }
        return $moduleInfo;
    }
}
