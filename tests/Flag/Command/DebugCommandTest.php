<?php

/*
 * This file is part of the maidmaid/flag package.
 *
 * (c) Dany Maillard <danymaillard93b@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maidmaid\Flag\Tests\Command;

use Maidmaid\Flag\Command\DebugCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class DebugCommandTest extends TestCase
{
    public function testA()
    {
        $application = new Application();
        $application->add(new DebugCommand());
        $command = $application->find('debug:flag');

        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--prefix' => 'E_',
            'bitfield' => '15'
        ));

        $output = $commandTester->getDisplay();

        $expected = <<<EOF

14 13 12 11 10 9  8  7  6  5  4  3  2  1  0                            
0  0  0  0  0  0  0  0  0  0  0  1  1  1  1                            
│  │  │  │  │  │  │  │  │  │  │  │  │  │  └── E_ERROR             1    
│  │  │  │  │  │  │  │  │  │  │  │  │  └───── E_WARNING           2    
│  │  │  │  │  │  │  │  │  │  │  │  └──────── E_PARSE             4    
│  │  │  │  │  │  │  │  │  │  │  └─────────── E_NOTICE            8    
│  │  │  │  │  │  │  │  │  │  └────────────── E_CORE_ERROR        16   
│  │  │  │  │  │  │  │  │  └───────────────── E_CORE_WARNING      32   
│  │  │  │  │  │  │  │  └──────────────────── E_COMPILE_ERROR     64   
│  │  │  │  │  │  │  └─────────────────────── E_COMPILE_WARNING   128  
│  │  │  │  │  │  └────────────────────────── E_USER_ERROR        256  
│  │  │  │  │  └───────────────────────────── E_USER_WARNING      512  
│  │  │  │  └──────────────────────────────── E_USER_NOTICE       1024 
│  │  │  └─────────────────────────────────── E_STRICT            2048 
│  │  └────────────────────────────────────── E_RECOVERABLE_ERROR 4096 
│  └───────────────────────────────────────── E_DEPRECATED        8192 
└──────────────────────────────────────────── E_USER_DEPRECATED   16384

EOF
        ;

        $this->assertEquals($expected, $output);
    }
}
