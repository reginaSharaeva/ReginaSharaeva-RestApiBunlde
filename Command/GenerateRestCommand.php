<?php

namespace ReginaSharaeva\RestApiBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use RestApiBundle\Generator\RestGenerator;
use RestApiBundle\Manipulator\RoutingManipulator;

class GenerateRestCommand extends Command
{
    protected $container;

    protected function configure() {
        $this->setDescription('Generates a REST api based on a Doctrine entity')
            ->setName('rest:generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $em = $this->container->get('doctrine.orm.entity_manager');
    	$entities = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

    	foreach ($entities as $key => $entity) {

            $metadata = $em->getClassMetadata($entity);

        	$generator = new RestGenerator($this->container->get('filesystem'));
        	$generator->generate($entity, $metadata);

            $output->writeln('Generating the REST api code: <info>OK</info>');
        	
        	$errors = array();
            $runner = $questionHelper->getRunner($output, $errors);

            $runner($this->updateRouting($output, $entity));

            $questionHelper->writeGeneratorSummary($output, $errors);
        }
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

}