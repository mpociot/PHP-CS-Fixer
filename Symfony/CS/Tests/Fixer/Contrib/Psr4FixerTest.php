<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class Psr4FixerTest extends AbstractFixerTestCase
{
    public function testFixCase()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/Contrib/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\cs\Fixer\Contrib;
class Psr4Fixer {}
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\cs\Fixer\Contrib;
class psr4Fixer {}
EOF;

        $this->doTest($expected, $input, $file);

        $expected = <<<'EOF'
<?php
class Symfony_CS_Fixer_Contrib_Psr4Fixer {}
EOF;
        $input = <<<'EOF'
<?php
class symfony_cs_FiXER_Contrib_Psr4FIXer {}
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testFixClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/Contrib/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\Contrib;
class Psr4Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\Contrib;
class blah {}
/* class foo */
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testFixAbstractClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/Contrib/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\Contrib;
abstract class Psr4Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\Contrib;
abstract class blah {}
/* class foo */
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testFixFinalClassName()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/Contrib/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\Contrib;
final class Psr4Fixer {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace Symfony\CS\Fixer\Contrib;
final class blah {}
/* class foo */
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testFixClassNameWithComment()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/Contrib/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace /* namespace here */ Symfony\CS\Fixer\Contrib;
class /* hi there */ Psr4Fixer /* why hello */ {}
/* class foo */
EOF;
        $input = <<<'EOF'
<?php
namespace /* namespace here */ Symfony\CS\Fixer\Contrib;
class /* hi there */ blah /* why hello */ {}
/* class foo */
EOF;

        $this->doTest($expected, $input, $file);
    }

    public function testHandlePartialNamespaces()
    {
        $file = $this->getTestFile(__DIR__.'/../../../Fixer/Contrib/Psr4Fixer.php');

        $expected = <<<'EOF'
<?php
namespace Foo\Bar\Baz\Fixer\Contrib;
class Psr4Fixer {}
EOF;

        $this->doTest($expected, null, $file);

        $expected = <<<'EOF'
<?php
namespace /* hi there */ Foo\Bar\Baz\Fixer\Contrib;
class /* hi there */ Psr4Fixer {}
EOF;

        $this->doTest($expected, null, $file);

        $expected = <<<'EOF'
<?php
namespace Foo\Bar\Baz;
class Psr4Fixer {}
EOF;

        $this->doTest($expected, null, $file);
    }

    /**
     * @dataProvider provideIgnoredCases
     */
    public function testIgnoreWrongNames($filename)
    {
        $file = $this->getTestFile($filename);

        $expected = <<<'EOF'
<?php
namespace Aaa;
class Bar {}
EOF;

        $this->doTest($expected, null, $file);
    }

    public function provideIgnoredCases()
    {
        return array(
            array('.php'),
            array('Foo.class.php'),
            array('4Foo.php'),
            array('$#.php'),
        );
    }
}
