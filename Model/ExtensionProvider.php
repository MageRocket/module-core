<?php
/**
 *  @author MageRocket
 *  @copyright Copyright (c) 2024 MageRocket (https://magerocket.com/)
 *  @link https://magerocket.com/
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
    )
    {
        $this->serializer = $serializer;
        $this->webservice = $webservice;
    }

    /**
     * @param $module
     * @return void
     */
    public function checkModuleUpdates($module)
    {
        $requestData = [];
        $requestData['headers'] = [
            "Content-Type" => "application/json"
        ];
        $endpoint = self::MAGEROCKET_UPDATE_ENDPOINT . "/$module";
        $magerocketResponse = $this->webservice->doRequest($endpoint, $requestData, "GET");
        $responseBody = $this->unserializeData($magerocketResponse->getBody()->getContents());
        if ($magerocketResponse->getStatusCode() > 201) {
            return ['version' => '1.0.0'];
        }
        return $responseBody;
    }

    /**
     * serializeData
     * @param $data
     * @return bool|string
     */
    private function serializeData($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * unserializeData
     * @param $data
     * @return bool|string
     */
    private function unserializeData($data)
    {
        return $this->serializer->unserialize($data);
    }
}