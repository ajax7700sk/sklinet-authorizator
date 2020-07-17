<?php declare(strict_types = 1);

namespace SklinetNette\Authorizator\Di;

use SklinetNette\Authorizator\Authorizator;
use SklinetNette\Authorizator\Latte\Functions;
use SklinetNette\Authorizator\Tracy\VoterPanel;
use Kdyby\Events\Diagnostics\Panel;
use Nette;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;
use Nette\PhpGenerator\PhpLiteral;
use Tracy\Debugger;

/**
 * Class AuthorizatorExtension
 *
 * @package SklinetNette\Authorizator\Di
 */
class AuthorizatorExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        /**
         * Load extension services
         */
        $this->registerServices();
    }

    public function beforeCompile()
    {
        parent::beforeCompile();

        /**
         * Add tracy panel
         */
        $this->addTracyPanel();

        /**
         * Register latte functions
         */
        $this->registerLatteFunctions();
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Register & load extension services
     */
    private function registerServices(): void
    {
        //get DIC builder
        $builder = $this->getContainerBuilder();

        $this->compiler->loadDefinitions(
            $builder,
            $this->loadFromFile(__DIR__ . '/services.neon')['services'],
            $this->name
        );
    }

    /**
     * Add extension tracy voter panel
     */
    private function addTracyPanel(): void
    {
        //get DIC builder
        $builder = $this->getContainerBuilder();
        //tracy service definition
        $barDefinition = $builder->getDefinition('tracy.bar');
        //check if tracy bar is ServiceDefinition
        assert($barDefinition instanceof ServiceDefinition);

        //add voter panel
        $barDefinition
            ->addSetup('addPanel', [
                new VoterPanel,
                $this->prefix('tracyPanel'),
            ]);
    }

    /**
     * Register extension latte functions
     */
    private function registerLatteFunctions(): void
    {
        //get DIC builder
        $builder = $this->getContainerBuilder();
        //latte factory service definition
        $latteFactoryDefinition = $builder->getDefinitionByType(ILatteFactory::class);
        //check if latte factory is ServiceDefinition
        assert($latteFactoryDefinition instanceof ServiceDefinition);

        //register 'isGranted' function
        $latteFactoryDefinition->addSetup(
            'addFunction',
            ['isGranted', ['@authorizator.latteFunctions', 'isGranted']]
        );
    }
}
