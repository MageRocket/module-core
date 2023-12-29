<?php

/**
 *  @author MageRocket
 *  @copyright Copyright (c) 2023 MageRocket (https://magerocket.com/)
 *  @link https://magerocket.com/
 */

namespace MageRocket\Core\Plugin;

use Magento\Backend\Model\Menu\Builder\AbstractCommand;
use MageRocket\Core\Helper\Data;

class MenuPlugin
{
    const MAGEROCKET_CORE = 'MageRocket_Core::menu';

    /**
     * @var Data $helper
     */
    protected Data $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param AbstractCommand $subject
     * @param $itemParams
     */
    public function afterExecute(AbstractCommand $subject, $itemParams)
    {
        if ($this->helper->isMenuEnabled()) {
            /**
             * Group all menus on MageRocket.
             */
            if (str_contains($itemParams['id'], 'MageRocket_') && isset($itemParams['parent'])
                && !str_contains($itemParams['parent'], 'MageRocket_')) {
                $itemParams['parent'] = self::MAGEROCKET_CORE;
            }
        } elseif ((isset($itemParams['id']) && $itemParams['id'] === self::MAGEROCKET_CORE)
            || (isset($itemParams['parent']) && $itemParams['parent'] === self::MAGEROCKET_CORE)) {
            $itemParams['removed'] = true;
        }

        return $itemParams;
    }
}
