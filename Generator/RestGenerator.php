<?php

namespace ReginaSharaeva\RestApiBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class RestGenerator extends Generator
{
    protected $filesystem;
    protected $entity;
    protected $metadata;
    protected $format;
    protected $actions;
    protected $routePrefix;
 
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function generate($entity, ClassMetadataInfo $metadata)
    {
        $this->actions = array('getById', 'getAll', 'post', 'put', 'delete');
        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The REST api generator does not support entity classes with multiple primary keys.');
        }
        if (!in_array('id', $metadata->identifier)) {
            throw new \RuntimeException('The REST api generator expects the entity object has a primary key field named "id" with a getId() method.');
        }
        $this->entity   = $entity;
        $this->metadata = $metadata;
        $this->generateControllerClass();
    }

    protected function generateControllerClass()
    {
        $bundle = substr($this->entity,0,strpos($this->entity,'\\Entity\\'));

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);
        
        $target = sprintf(
            '%s/Controller/%s/%sRestController.php',
            $entityNamespace,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );
        if (file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile(
            'rest/controller.php.twig',
            $target,
            array(
                'actions' => $this->actions,
                'entity' => $this->entity,
                'entity_class' => $entityClass,
                'namespace' => $bundle,
                'entity_namespace' => $entityNamespace,
            )
        );
    }
}
