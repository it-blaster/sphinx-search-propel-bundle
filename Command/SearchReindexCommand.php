<?php
namespace ItBlaster\SphinxSearchPropelBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchReindexCommand extends ContainerAwareCommand
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
            ->setName('it-blaster:search-reindex')
            ->setDescription('Запуск переиндексации сайта сфинксом')
//            ->addArgument('name',InputArgument::OPTIONAL,'Who do you want to greet?')
//            ->addOption('yell',null,InputOption::VALUE_NONE,'If set, the task will yell in uppercase letters')
            ->setHelp(<<<EOF
Запуск переиндексации сайта сфинксом

<info>php %command.full_name%</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $root_dir = $this->getContainer()->get('kernel')->getRootDir();
        $sphinx_conf = $this->getContainer()->getParameter('sphinx_conf_path');
        $script = "cd $root_dir && indexer --config $sphinx_conf --All --rotate";
        echo `$script`;
    }
}