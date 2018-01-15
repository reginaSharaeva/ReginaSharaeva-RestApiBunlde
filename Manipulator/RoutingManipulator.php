<?php

namespace ReginaSharaeva\RestApiBundle\Manipulator;

use Sensio\Bundle\GeneratorBundle\Manipulator\Manipulator;
use Symfony\Component\DependencyInjection\Container;

class RoutingManipulator extends Manipulator
{
    private $file;
    
    public function __construct($file)
    {
        $this->file = $file;
    }
  
    public function addResource($bundle, $prefix = '/', $entity)
    {
        $current = '';
        $code    = sprintf("%s:\n", Container::underscore(substr($bundle, 0, -6)) . '_' . Container::underscore($entity) . ('/' !== $prefix ? '_' . str_replace('/', '_', substr($prefix, 1)) : ''));
        if (file_exists($this->file)) {
            $current = file_get_contents($this->file);
            // Don't add same bundle twice
            if (false !== strpos($current, $code)) {
                throw new \RuntimeException(sprintf('Bundle "%s" is already imported.', $bundle));
            }
        } elseif (!is_dir($dir = dirname($this->file))) {
            mkdir($dir, 0777, true);
        }
        $code .= sprintf("    resource: \"@%s/Controller/%sRESTController.php\"\n", $bundle, $entity);
        $code .= sprintf("    type:   %s\n", "rest");
        $code .= sprintf("    prefix:   %s\n", $prefix);
        $code .= "\n";
        $code .= $current;
        if (false === file_put_contents($this->file, $code)) {
            return false;
        }
        return true;
    }
}