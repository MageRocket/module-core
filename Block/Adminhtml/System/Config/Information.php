<?php
/**
 * @author MageRocket
 * @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 * @link https://magerocket.com/
 */

namespace MageRocket\Core\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Information extends Fieldset
{
    /**
     * @var string $modulePage
     */
    protected string $modulePage = 'https://magerocket.com/core';

    /**
     * @var string $moduleCode
     */
    protected string $moduleCode = 'MageRocket_Core';

    /**
     * @var string $moduleLogo
     */
    protected string $moduleLogo = '';

    /**
     * @var string $moduleName
     */
    protected string $moduleName = '';

    /**
     * @var string $moduleDescription
     */
    protected string $moduleDescription = '';

    /**
     * @var bool $allowFeatureRequest
     */
    protected bool $allowFeatureRequest = true;

    /**
     * @var $userGuide
     */
    protected $userGuide = false;

    /**
     * @var string $content
     */
    protected $content;

    /**
     * Render fieldset html
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $html = $this->_getHeaderHtml($element);
        $this->_eventManager->dispatch(
            'magerocket_core_information',
            ['block' => $this]
        );
        $html .= $this->getContent();
        $html .= $this->_getFooterHtml($element);
        $html = str_replace(
            'magerocket_information]" type="hidden" value="0"',
            'magerocket_information]" type="hidden" value="1"',
            $html
        );
        return preg_replace('(onclick=\"Fieldset.toggleCollapse.*?\")', '', $html);
    }

    /**
     * getUserGuide
     */
    public function getUserGuide()
    {
        return $this->userGuide;
    }

    /**
     * setUserGuide
     * @param string $userGuide
     * @return void
     */
    public function setUserGuide(string $userGuide): void
    {
        $this->userGuide = $userGuide;
    }

    /**
     * getModuleCode
     * @return string
     */
    public function getModuleCode(): string
    {
        return $this->moduleCode;
    }

    /**
     * setModuleCode
     * @param string $moduleCode
     * @return void
     */
    public function setModuleCode(string $moduleCode): void
    {
        $this->moduleCode = $moduleCode;
    }

    /**
     * getModulePage
     * @return string
     */
    public function getModulePage(): string
    {
        return $this->modulePage;
    }

    /**
     * setModulePage
     * @param string $modulePage
     * @return void
     */
    public function setModulePage(string $modulePage): void
    {
        $this->modulePage = $modulePage;
    }

    /**
     * showButtonFeatureRequest
     * @return bool
     */
    public function showButtonFeatureRequest(): bool
    {
        return $this->allowFeatureRequest;
    }

    /**
     * getModuleName
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * setModuleName
     * @param string $moduleName
     * @return void
     */
    public function setModuleName(string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    /**
     * getModuleLogo
     * @return string
     */
    public function getModuleLogo(): string
    {
        return $this->moduleLogo;
    }

    /**
     * setModuleLogo
     * @param string $moduleLogo
     * @return void
     */
    public function setModuleLogo(string $moduleLogo): void
    {
        $this->moduleLogo = $moduleLogo;
    }

    /**
     * getModuleDescription
     * @return string
     */
    public function getModuleDescription(): string
    {
        return $this->moduleDescription;
    }

    /**
     * setModuleDescription
     * @param string $moduleDescription
     * @return void
     */
    public function setModuleDescription(string $moduleDescription): void
    {
        $this->moduleDescription = $moduleDescription;
    }
}
