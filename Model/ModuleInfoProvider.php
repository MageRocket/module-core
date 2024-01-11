<?php
/**
 *  @author MageRocket
 *  @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 *  @link https://magerocket.com/
 */

namespace MageRocket\Core\Model;

use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Serialize\SerializerInterface as Json;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir\Reader;

class ModuleInfoProvider
{
    /**
     * @var Reader $moduleReader
     */
    protected Reader $moduleReader;

    /**
     * @var File $filesystem
     */
    protected File $filesystem;

    /**
     * @var Json $jsonSerializer
     */
    protected Json $jsonSerializer;

    /**
     * @var ComponentRegistrar $componentRegistrar
     */
    protected ComponentRegistrar $componentRegistrar;

    /**
     * @var ReadFactory $readFactory
     */
    protected ReadFactory $readFactory;

    /**
     * @param File $filesystem
     * @param Json $jsonSerializer
     * @param Reader $moduleReader
     * @param ReadFactory $readFactory
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        File $filesystem,
        Json $jsonSerializer,
        Reader $moduleReader,
        ReadFactory $readFactory,
        ComponentRegistrar $componentRegistrar
    )
    {
        $this->filesystem = $filesystem;
        $this->readFactory = $readFactory;
        $this->moduleReader = $moduleReader;
        $this->jsonSerializer = $jsonSerializer;
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * getModuleInfo
     * @param string $moduleCode
     * @return array
     */
    public function getModuleInfo(string $moduleCode = 'MageRocket_Core'): array
    {
        $modulePath = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            $moduleCode
        );
        $directoryRead = $this->readFactory->create($modulePath);
        try {
            $composerJsonData = $directoryRead->readFile('composer.json');
        } catch (\Exception $e) {
            return [];
        }
        $data = $this->jsonSerializer->unserialize($composerJsonData ?? '');
        return $data ?? [];
    }
}