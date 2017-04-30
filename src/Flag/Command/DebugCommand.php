<?php

/*
 * This file is part of the maidmaid/flag package.
 *
 * (c) Dany Maillard <danymaillard93b@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maidmaid\Flag\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Maidmaid\Flag\Flag;

class DebugCommand extends Command
{
    private $legends = array();
    private $max;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('debug:flag')
            ->addArgument('bitfield', InputArgument::OPTIONAL, '', 0)
            ->addOption('from', 'f', InputOption::VALUE_REQUIRED, 'Class where searching flags is made')
            ->addOption('prefix', 'p', InputOption::VALUE_REQUIRED, 'Prefix flags that filter search result')
            ->addOption('hierarchical', 'l', InputOption::VALUE_NONE, 'Defines flags as hierarchical')
            ->setHelp(<<<EOF
Examples of <info>%command.name%</info> command:

  <info>php %command.full_name% --prefix E_</info>
  <info>php %command.full_name% --prefix E_ 8</info>
  <info>php %command.full_name% --from Symfony\\\\Component\\\\Console\\\\Output\\\\Output --prefix VERBOSITY_ --hierarchical 64</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bitfield = (int) $input->getArgument('bitfield');
        $from = $input->getOption('from');
        $prefix = $input->getOption('prefix') ? $input->getOption('prefix') : '';
        $hierarchical = $input->getOption('hierarchical');

        $flag = Flag::create($from, $prefix, $hierarchical, $bitfield);

        $flags = array();
        $flagged = array();
        $iterator = $flag->getIterator(false);
        $iterator->ksort();
        foreach ($iterator as $v => $f) {
            // Check if it's a top flag
            if (((int) ($r = log($v, 2))) == $r) {
                $flags[$r] = $f;
                $flagged[$r] = $flag->has($v) ? '<bg=green;fg=black;options=bold>1</>' : 0;
            }
        }
        $this->max = max(array_keys($flags));

        $headers = array();
        $bitfield = array();
        for ($i = $this->max; $i >= 0; --$i) {
            $headers[] = $i;
            $bitfield[] = isset($flagged[$i]) ? $flagged[$i] : 0;
        }

        $i = -1;
        foreach ($flags as $x => $flag) {
            $this->legend($this->max - $x, ++$i, $flag, $from, $flagged[$x]);
        }

        $rows = $this->legends;
        array_unshift($rows, $bitfield);

        $table = (new Table($output))
            ->setColumnWidths(array_fill(0, count($headers), 3))
            ->setHeaders($headers)
            ->setRows($rows)
            ->setStyle('compact')
        ;

        $table->getStyle()
            ->setVerticalBorderChar('')
            ->setCrossingChar('')
        ;

        $output->writeln('');
        $table->render();
    }

    private function legend($x, $y, $name, $from, $highlight = false)
    {
        $format = $highlight ? '<info>%s</info>' : '%s';

        while (!isset($this->legends[$y])) {
            $this->legends[] = array_fill(0, $this->max + 2, '');
        }

        $this->legends[$y][$x] = sprintf($format, '└──');
        $this->legends[$y][$this->max + 1] = sprintf($format, ' '.$name);
        $this->legends[$y][$this->max + 2] = sprintf($format, ' '.constant($from ? $from.'::'.$name : $name));

        // h
        for ($i = $x + 1; $i <= $this->max; ++$i) {
            $this->legends[$y][$i] = sprintf($format, '───');
        }

        // v
        for ($i = $y - 1; $i >= 0; --$i) {
            $this->legends[$i][$x] = sprintf($format, '│');
        }
    }
}
