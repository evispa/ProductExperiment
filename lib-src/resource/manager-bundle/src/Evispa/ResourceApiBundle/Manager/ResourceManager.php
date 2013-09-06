<?php

namespace Evispa\ResourceApiBundle\Manager;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Evispa\ObjectMigration\VersionConverter;
use Evispa\ObjectMigration\VersionReader;
use Evispa\ResourceApiBundle\Backend\FindParameters;
use Evispa\ResourceApiBundle\Backend\Unicorn;
use Evispa\ResourceApiBundle\Backend\UnicornBackend;
use Symfony\Component\Form\Exception\LogicException;

/**
 * @author nerijus
 */
class ResourceManager
{
    /**
     * Used to read/write resource properties.
     *
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $propertyAccess;

    /**
     * @var \ReflectionClass
     */
    private $class;

    /**
     * Resource property list from config, (property.id) => (property).
     *
     * @var type
     */
    private $resourceProperties;

    /**
     * Version reader.
     *
     * @var VersionReader
     */
    private $versionReader;

    /**
     * Version converter for each resource part.
     *
     * @var VersionConverter[]
     */
    private $partVersionConverter;

    /**
     * Unicorn - backend configuration set.
     *
     * @var Unicorn
     */
    private $unicorn;

    /**
     * Used converter options.
     *
     * @var array
     */
    private $converterOptions;

    /**
     * @param Reader        $reader
     * @param VersionReader $versionReader
     * @param array         $converterOptions
     * @param \ReflectionClass $class
     * @param array            $resourceProperties
     * @param Unicorn       $unicorn
     *
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function __construct(
        Reader $reader,
        VersionReader $versionReader,
        array $converterOptions,
        \ReflectionClass $class,
        array $resourceProperties,
        Unicorn $unicorn
    ) {
        $this->versionReader = $versionReader;
        $this->propertyAccess = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();
        $this->class = $class;
        $this->resourceProperties = $resourceProperties;
        $this->unicorn = $unicorn;
        $this->converterOptions = $converterOptions;

        foreach ($this->resourceProperties as $partName => $propertyName) {
            $property = $reader->getPropertyAnnotation(
                $this->class->getProperty($propertyName),
                'JMS\Serializer\Annotation\Type'
            );

            if (null === $property) {
                throw new LogicException(
                    'Resource "' . $this->class->getName() .
                    '" property "' . $propertyName .
                    '" should have JMS\Serializer\Annotation\Type annotation.'
                );
            }

            $this->partVersionConverter[$partName] = new VersionConverter(
                $versionReader,
                $property->name,
                $converterOptions
            );
        }
    }

    /**
     * Get the name of managed class.
     *
     * @return string
     */
    public function getClassName() {
        return $this->class->getName();
    }

    /**
     * Get version reader used by this manager.
     *
     * @return VersionReader
     */
    public function getVersionReader() {
        return $this->versionReader;
    }

    /**
     * Get used converter options.
     *
     * @return array
     */
    public function getConverterOptions()
    {
        return $this->converterOptions;
    }

    /**
     * @param UnicornBackend $unicornBackend
     *
     * @return array
     */
    private function getBackendPartNames($unicornBackend)
    {
        $backendPartClasses = $unicornBackend->getManagedParts();
        $backendPartNames = array_keys($backendPartClasses);

        return $backendPartNames;
    }

    private function findOneUnicornParts($slug, $unicornBackend) {
        $realBackend = $unicornBackend->getBackend();
        $backendPartNames = $this->getBackendPartNames($unicornBackend);

        return $realBackend->findOne($slug, $backendPartNames);
    }

    /**
     * @param UnicornBackend $unicornBackend
     * @param array          $parts
     * @param                $resource
     *
     * @throws \LogicException
     */
    private function updateResourceForParts($unicornBackend, $parts, $resource)
    {
        $realBackend = $unicornBackend->getBackend();
        $backendPartClasses = $unicornBackend->getManagedParts();
        $backendPartNames = array_keys($backendPartClasses);

        foreach ($backendPartNames as $partName) {
            if (!isset($parts[$partName])) {
                throw new \LogicException(
                    'Expected part "' . $partName . '" not found in backend "' . get_class($realBackend) . '".'
                );
            }

            $part = $this->partVersionConverter[$partName]->migrateFrom($parts[$partName]);

            $this->propertyAccess->setValue($resource, $this->resourceProperties[$partName], $part);
        }
    }

    /**
     * Find a single resource object.
     *
     * @param string $slug Resource identifier.
     *
     * @throws \LogicException
     *
     * @return \Evispa\Api\Resource\Model\ApiResourceInterface
     */
    public function findOne($slug)
    {
        $resource = $this->class->newInstance();
        $resource->setSlug($slug);

        $backends = $this->unicorn->getBackends();

        // Make sure there is at least 1 backend.

        if (0 === count($backends)) {
            return null;
        }

        // First backend is "primary" backend, put others in array.

        $primaryBackend = $backends[0];
        /** @var UnicornBackend[] $otherBackends */
        $otherBackends = array_slice($backends, 1);

        // Execute first backend, if it does not find anything, return null.

        $primaryParts = $this->findOneUnicornParts($slug, $primaryBackend);
        if (null === $primaryParts) {
            return null;
        }

        $this->updateResourceForParts($primaryBackend, $primaryParts, $resource);

        // Execute other backends, if they do not find anything, ignore.

        foreach ($otherBackends as $unicornBackend) {
            $otherParts = $this->findOneUnicornParts($slug, $unicornBackend);
            if (null === $otherParts) {
                continue;
            }
            $this->updateResourceForParts($unicornBackend, $otherParts, $resource);
        }

        return $resource;
    }

    /**
     * TODO: finish
     *
     * @param FindParameters $params
     */
    public function find(FindParameters $params)
    {
        $backends = $this->unicorn->getBackends();

        // Make sure there is at least 1 backend.

        if (0 === count($backends)) {
            return null;
        }

        $primaryBackend = $backends[0];
        /** @var UnicornBackend[] $otherBackends */
        $otherBackends = array_slice($backends, 1);

        // Execute first backend, if it does not find anything, return null.
        $primaryParts = $this->getFindPartsFromUnicorn($params, $primaryBackend);
        if (null === $primaryParts) {
            return null;
        }

        foreach ($primaryParts as $part) {

        }

    }

    /**
     * Create and get a new resource object, no persist to the db.
     *
     * @return \Evispa\Api\Resource\Model\ApiResourceInterface
     */
    public function getNew()
    {

    }

    /**
     * Save a resource object to the database.
     *
     * @param \Evispa\Api\Resource\Model\ApiResourceInterface $resource
     */
    public function saveOne($resource)
    {

    }
}