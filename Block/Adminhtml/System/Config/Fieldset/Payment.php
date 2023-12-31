<?php
/**
 *  @author MageRocket
 *  @copyright Copyright (c) 2023 MageRocket (https://magerocket.com/)
 *  @link https://magerocket.com/
 */

namespace MageRocket\Core\Block\Adminhtml\System\Config\Fieldset;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Config\Model\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class Payment extends Fieldset
{
    /**
     * @var Config $_backendConfig
     */
    protected Config $_backendConfig;

    /**
     * @var SecureHtmlRenderer $secureRenderer
     */
    protected SecureHtmlRenderer $secureRenderer;

    /**
     * @var Data $helper
     */
    protected Data $helper;

    /**
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param Config $backendConfig
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Config $backendConfig,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->_backendConfig = $backendConfig;
        $secureRenderer = $secureRenderer ?? ObjectManager::getInstance()->get(SecureHtmlRenderer::class);
        parent::__construct($context, $authSession, $jsHelper, $data, $secureRenderer);
        $this->secureRenderer = $secureRenderer;
    }

    /**
     * Add custom css class
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getFrontendClass($element)
    {
        $enabledString = $this->_isPaymentEnabled($element) ? ' enabled' : '';
        return parent::_getFrontendClass($element) . ' with-button' . $enabledString;
    }

    /**
     * Check whether current payment method is enabled
     *
     * @param AbstractElement $element
     * @return bool
     */
    protected function _isPaymentEnabled($element)
    {
        $groupConfig = $element->getGroup();
        $activityPaths = isset($groupConfig['activity_path']) ? $groupConfig['activity_path'] : [];

        if (!is_array($activityPaths)) {
            $activityPaths = [$activityPaths];
        }

        $isPaymentEnabled = false;
        foreach ($activityPaths as $activityPath) {
            $isPaymentEnabled = $isPaymentEnabled
                || (string)$this->_backendConfig->getConfigDataValue($activityPath);
        }

        return $isPaymentEnabled;
    }

    /**
     * Return header title part of html for payment solution
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading">';
        $groupConfig = $element->getGroup();
        $htmlId = $element->getHtmlId();
        // Generate Button
        $configureSpan = '<span class="state-closed">' . __('Configure') . '</span>';
        $closeSpan = '<span class="state-opened">' . __('Close') . '</span>';
        $paymentButton = "<button type='button' id='$htmlId-head' class='button action-configure'>{$configureSpan}{$closeSpan}</button>";
        // Button EventJs
        $attributeUrl = $this->getUrl('adminhtml/*/state');
        $eventJs = /* @noEscape */ $this->secureRenderer->renderEventListenerAsTag(
            'onclick',
            "mageRocketPayment.call(this, '$htmlId', '$attributeUrl');event.preventDefault()",
            "button#{$htmlId}-head"
        );
        // Container;
        $html .= "<div class='button-container'>$paymentButton</div>";
        $html .= "<div class='heading'><strong>{$element->getLegend()}</strong>";
        if ($element->getComment()) {
            $html .= "<span class='headheading-introing'>{$element->getComment()}</span>";
        }
        $html .= '<div class="config-alt"></div>';
        $html .= "</div></div>$eventJs";
        return $html;
    }

    /**
     * Return header comment part of html for payment solution
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * Get collapsed state on-load
     *
     * @param AbstractElement $element
     * @return false
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _isCollapseState($element)
    {
        return false;
    }

    /**
     * Return extra Js.
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getExtraJs($element)
    {
        $script = "require(['jquery', 'prototype'], function(jQuery){
            window.mageRocketPayment = function (id, url) {
                var doScroll = false;
                Fieldset.toggleCollapse(id, url);
                if ($(this).hasClassName(\"open\")) {
                    \$$(\".with-button button.button\").each(function(anotherButton) {
                        if (anotherButton != this && $(anotherButton).hasClassName(\"open\")) {
                            $(anotherButton).click();
                            doScroll = true;
                        }
                    }.bind(this));
                }
                if (doScroll) {
                    var pos = Element.cumulativeOffset($(this));
                    window.scrollTo(pos[0], pos[1] - 45);
                }
            }
        });";
        return $this->_jsHelper->getScript($script);
    }
}
