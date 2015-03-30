<?php
namespace ItBlaster\SphinxSearchPropelBundle\Command;

use ItBlaster\SphinxSearchPropelBundle\Bridge\PropelBridge;
use ItBlaster\SphinxSearchPropelBundle\Search\Sphinxsearch;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchIndexesCommand extends ContainerAwareCommand
{
    protected $output;

    /**
     * Вывод ссобщения в консоль
     *
     * @param $message
     */
    protected function log($message)
    {
        //$output->writeln('<info>green color</info>');
        //$output->writeln('<comment>yellow text</comment>');
        $this->output->writeln($message);
    }

    protected function configure()
    {
        $this
            ->setName('it-blaster:search-indexes')
            ->setDescription("Список индексов")
//            ->addArgument('name',InputArgument::OPTIONAL,'Who do you want to greet?')
//            ->addOption('yell',null,InputOption::VALUE_NONE,'If set, the task will yell in uppercase letters')
            ->setHelp(<<<EOF
Список индексов

<info>php %command.full_name%</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        //$searchd = $this->container->get('it_blaster.sphinx_search_propel.search');
        /** @var Sphinxsearch $searchd */
        $searchd = $this->getContainer()->get('it_blaster.sphinx_search_propel.search');
        /** @var PropelBridge $propel_bridge */
        $propel_bridge = $searchd->getBridge();
        if (count($propel_bridge->getIndexes())) {
            foreach ($propel_bridge->getIndexes() as $index_name => $model_path) {
                $this->log("<comment>$index_name</comment>: <info>$model_path</info>" );
            }
        }
    }
}