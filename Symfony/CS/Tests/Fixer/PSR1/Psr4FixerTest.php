<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR1;

use Symfony\CS\Config\Config;
use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 */
final class Psr4FixerTest extends AbstractFixerTestBase
{
    public function testFixCase()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR1/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\cs\Fixer\PSR1;
class Psr4Fixer {}
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\cs\Fixer\PSR1;
class psr4Fixer {}
EOF;

        $this->makeTest($expected, $input, $file);

        $expected = <<<'EOF'
<?php
class Symfony_CS_Fixer_PSR1_Psr4Fixer {}
EOF;
        $input = <<<'EOF'
<?php
class symfony_cs_FiXER_PSR1_Psr4FIXer {}
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testFixClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR1/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR1;
class Psr4Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR1;
class blah {}
/* class foo */
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testFixAbstractClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR1/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR1;
abstract class Psr4Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR1;
abstract class blah {}
/* class foo */
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testFixFinalClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR1/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR1;
final class Psr4Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\PSR1;
final class blah {}
/* class foo */
EOF;

        $this->makeTest($expected, $input, $file);
    }

    public function testHandlePartialNamespaces()
    {
        $fixer = $this->getFixer();
        $config = new Config();
        $config->setDir(__DIR__.'/../../../');
        $fixer->setConfig($config);

        $file = $this->getTestFile(__DIR__.'/../../../Fixer/PSR1/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Foo\Bar\Baz\Fixer\PSR1;
class Psr4Fixer {}
EOF;

        $this->makeTest($expected, null, $file, $fixer);

        $config->setDir(__DIR__.'/../../../Fixer/PSR1');
        $expected = <<<'EOF'
<?php
namespace Foo\Bar\Baz;
class Psr4Fixer {}
EOF;

        $this->makeTest($expected, null, $file, $fixer);
    }

    public function testIgnoreLongExtension()
    {
        $file = $this->getTestFile('Foo.class.php');

        $expected = <<<'EOF'
<?php
namespace Aaa;
class Bar {}
EOF;

        $this->makeTest($expected, null, $file);
    }
}
