<?php
/**
 * @author MageRocket
 * @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 * @link https://magerocket.com/
 */

namespace MageRocket\Core\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Guides extends Action
{
    const ADMIN_RESOURCE = 'MageRocket_Core::guides';

    /**
     * @return void
     */
    public function execute()
    {
        $this->_response->setRedirect(
            'https://docs.magerocket.com/?utm_source=sidebar&utm_medium=link&utm_content=user-guide'
        );
    }
}