<?php
/**
 *  @author MageRocket
 *  @copyright Copyright (c) 2023 MageRocket (https://magerocket.com/)
 *  @link https://magerocket.com/
 */

namespace MageRocket\Core\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\PackageInfoFactory;

/**
 * Backend system config datetime field renderer
 */
class Version extends Field
{
    /**
     * @var PackageInfoFactory $packageInfoFactory
     */
    protected PackageInfoFactory $packageInfoFactory;

    /**
     * @param Context $context
     * @param PackageInfoFactory $packageInfoFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        PackageInfoFactory $packageInfoFactory,
        array $data = []
    ) {
        $this->packageInfoFactory = $packageInfoFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $packageInfo = $this->packageInfoFactory->create();
        $version = $packageInfo->getVersion($originalData['module_name']);
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
