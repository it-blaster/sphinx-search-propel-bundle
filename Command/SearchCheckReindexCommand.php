<?php
namespace ItBlaster\SphinxSearchPropelBundle\Command;

use ItBlaster\SphinxSearchPropelBundle\Model\ReindexQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchCheckReindexCommand extends ContainerAwareCommand
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
            ->setName('it-blaster:search-check-reindex')
            ->setDescription('Проверяет, нужно ли переиндексировать сайт сфинксом')
//            ->addArgument('name',InputArgument::OPTIONAL,'Who do you want to greet?')
//            ->addOption('yell',null,InputOption::VALUE_NONE,'If set, the task will yell in uppercase letters')
            ->setHelp(<<<EOF
Проверяет, нужно ли переиндексировать сайт сфинксом

<info>php %command.full_name%</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        if ($object_reindex = ReindexQuery::create()->findOneByStatus(1)) {
            $object_reindex->setStatus(2)->save();
            $root_dir = $this->getContainer()->get('kernel')->getRootDir();
            $sphinx_conf = $this->getContainer()->getParameter('sphinx_conf_path');
            $script = "cd $root_dir && indexer --config $sphinx_conf --All --rotate";
            echo `$script`;
            $object_reindex->setStatus(0)->save();
        } else {
            $this->log('nothing to reindex');
        }
    }
}