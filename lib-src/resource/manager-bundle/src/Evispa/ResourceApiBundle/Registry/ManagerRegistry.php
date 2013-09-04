<?php

namespace Evispa\ResourceApiBundle\Registry;

/**
 * @author nerijus
 */
class ManagerRegistry
{
    private $loadedManagers = array();

    protected $apiConfigRegistry;

    /**
     * Set api config registry.
     *
     * @param ApiConfigRegistry $apiConfigRegistry
     */
    public function setApiConfigRegistry(ApiConfigRegistry $apiConfigRegistry) {
        $this->apiConfigRegistry = $apiConfigRegistry;
    }

    protected function loadManagerForConfig(\Evispa\ResourceApiBundle\Config\ResourceApiConfig $config) {
        $manager = new \Evispa\ResourceApiBundle\Manager\ResourceManager($config->getResourceClass());

        return $manager;
    }

    /**
     * Get resource manager.
     *
     * @param string $resourceId
     *
     * @return \Evispa\ResourceApiBundle\Manager\ResourceManager
     */
    public function getResourceManager($resourceId) {
        if (isset($this->loadedManagers[$resourceId])) {
            return $this->loadedManagers[$resourceId];
        }

        $resourceConfig = $this->apiConfigRegistry->getResourceConfig($resourceId);
        if (null === $resourceConfig) {
            return null;
        }

        $this->loadedManagers[$resourceId] = $this->loadManagerForConfig($resourceConfig);

        return $this->loadedManagers[$resourceId];
    }
}