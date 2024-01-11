<?php
/**
 *  @author MageRocket
 *  @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 *  @link https://magerocket.com/
 */

namespace MageRocket\Core\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use MageRocket\Core\Model\ExtensionProvider;

/**
 * Backend system config datetime field renderer
 */
class LatestVersion extends Field
{
    /**
     * @var ExtensionProvider $extensionProvider
     */
    protected ExtensionProvider $extensionProvider;

    /**
     * @param Context $context
     * @param ExtensionProvider $extensionProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        ExtensionProvider $extensionProvider,
        array $data = []
    ) {
        $this->extensionProvider = $extensionProvider;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $moduleData = $this->extensionProvider->checkModuleUpdates($originalData['module_name']);
        $version = $moduleData['version'];
        return "<strong>$version</strong>";
    }

    /**
     * @param AbstractElement $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml(AbstractElement $element, $html)
    {
        return "<tr id='row_{$element->getHtmlId()}' class='magerocket_generic_group'>{$html}</tr>";
    }
}
