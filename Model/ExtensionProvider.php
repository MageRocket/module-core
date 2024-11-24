<?php
/**
 * @author MageRocket
 * @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 * @link https://magerocket.com/
 */

namespace MageRocket\Core\Model;

use MageRocket\Core\Model\Rest\Webservice;
use Magento\Framework\Serialize\SerializerInterface as Json;

class ExtensionProvider
{
    private const MAGEROCKET_UPDATE_ENDPOINT = 'https://magerocket.com/check/module';

    /**
     * @var Webservice $webservice
     */
    protected Webservice $webservice;

    /**
     * @var Json $serializer
     */
    protected Json $serializer;

    /**
     * @param Json $serializer
     * @param Webservice $webservice
     */
    public function __construct(
        Json $serializer,
        Webservice $webservice
    ) {
        $this->serializer = $serializer;
        $this->webservice = $webservice;
    }

    /**
     * Check Module Updates
     *
     * @param string $module
     * @return array
     */
    public function checkModuleUpdates(string $module)
    {
        $requestData = [];
        $requestData['headers'] = [
            "Content-Type" => "application/json",
            "User-Agent"   => "MageRocketCore/1.5"
        ];
        $endpoint = self::MAGEROCKET_UPDATE_ENDPOINT . "/$module";
        $magerocketResponse = $this->webservice->doRequest($endpoint, $requestData, "GET");
        if ($magerocketResponse->getStatusCode() > 201 ||
            strpos($magerocketResponse->getHeader('content-type')[0], 'application/json') === false
        ) {
            return ['version' => '1.0.0'];
        }
        return $this->unserializeData($magerocketResponse->getBody()->getContents());
    }

    /**
     * Serialize Data
     *
     * @param $data
     * @return bool|string
     */
    private function serializeData($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * Unserialize Data
     *
     * @param $data
     * @return bool|string
     */
    private function unserializeData($data)
    {
        return $this->serializer->unserialize($data);
    }
}
