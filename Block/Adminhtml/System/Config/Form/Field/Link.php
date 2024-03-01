<?php
/**
 * @author MageRocket
 * @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 * @link https://magerocket.com/
 */

namespace MageRocket\Core\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Link extends Field
{
    /**
     * @var string $linkLabel
     */
    protected string $linkLabel = 'MageRocket Config';

    /**
     * @var string $linkPath
     */
    protected string $linkPath = '#';

    /**
     * Render Field
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     * @param  AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return sprintf(
            '<a href ="%s">%s</a>',
            rtrim($this->_urlBuilder->getUrl($this->getLinkPath()), '/'),
            $this->getLinkLabel()
        );
    }

    /**
     * setLinkLabel
     * @param string $linkLabel
     * @return string
     */
    public function setLinkLabel(string $linkLabel)
    {
        $this->linkLabel = $linkLabel;
    }

    /**
     * getLinkLabel
     * @return string
     */
    public function getLinkLabel(): string
    {
        return $this->linkLabel;
    }

    /**
     * setLinkPath
     * @param string $linkPath
     * @return string
     */
    public function setLinkPath(string $linkPath)
    {
        $this->linkPath = $linkPath;
    }

    /**
     * getLinkPath
     * @return string
     */
    public function getLinkPath(): string
    {
        return $this->linkPath;
    }
}
