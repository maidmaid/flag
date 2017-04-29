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
 │  │  │  │  │  │  │  │  │  │  │  │  │  │  └─ E_ERROR             
 │  │  │  │  │  │  │  │  │  │  │  │  │  └─ ── E_WARNING           
 │  │  │  │  │  │  │  │  │  │  │  │  └─ ── ── E_PARSE             
 │  │  │  │  │  │  │  │  │  │  │  └─ ── ── ── E_NOTICE            
 │  │  │  │  │  │  │  │  │  │  └─ ── ── ── ── E_CORE_ERROR        
 │  │  │  │  │  │  │  │  │  └─ ── ── ── ── ── E_CORE_WARNING      
 │  │  │  │  │  │  │  │  └─ ── ── ── ── ── ── E_COMPILE_ERROR     
 │  │  │  │  │  │  │  └─ ── ── ── ── ── ── ── E_COMPILE_WARNING   
 │  │  │  │  │  │  └─ ── ── ── ── ── ── ── ── E_USER_ERROR        
 │  │  │  │  │  └─ ── ── ── ── ── ── ── ── ── E_USER_WARNING      
 │  │  │  │  └─ ── ── ── ── ── ── ── ── ── ── E_USER_NOTICE       
 │  │  │  └─ ── ── ── ── ── ── ── ── ── ── ── E_STRICT            
 │  │  └─ ── ── ── ── ── ── ── ── ── ── ── ── E_RECOVERABLE_ERROR 
 │  └─ ── ── ── ── ── ── ── ── ── ── ── ── ── E_DEPRECATED        
 └─ ── ── ── ── ── ── ── ── ── ── ── ── ── ── E_USER_DEPRECATED   

EOF
        ;

        $this->assertEquals($expected, $output);
    }
}
