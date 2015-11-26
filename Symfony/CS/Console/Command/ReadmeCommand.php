<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class ReadmeCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('readme')
            ->setDescription('Generates the README content, based on the fix command help')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $header = <<<EOF
PHP Coding Standards Fixer
==========================

The PHP Coding Standards Fixer tool fixes *most* issues in your code when you
want to follow the PHP coding standards as defined in the PSR-1 and PSR-2
documents and many more.

If you are already using a linter to identify coding standards problems in your
code, you know that fixing them by hand is tedious, especially on large
projects. This tool does not only detect them, but also fixes them for you.

This is StyleCI's custom vesion.

Usage
-----

EOF;

        $command = $this->getApplication()->get('fix');
        $help = $command->getHelp();
        $help = str_replace('%command.full_name%', 'php-cs-fixer.phar '.$command->getName(), $help);
        $help = str_replace('%command.name%', $command->getName(), $help);
        $help = preg_replace('#</?(comment|info)>#', '``', $help);
        $help = preg_replace('#^(\s+)``(.+)``$#m', '$1$2', $help);
        $help = preg_replace('#^ \* ``(.+)``#m', '* **$1**', $help);
        $help = preg_replace("#^\n( +)#m", "\n.. code-block:: bash\n\n$1", $help);
        $help = preg_replace("#^\.\. code-block:: bash\n\n( +<\?(\w+))#m", ".. code-block:: $2\n\n$1", $help);
        $help = preg_replace_callback(
            "#^\s*<\?(\w+).*?\?>#ms",
            function ($matches) {
                $result = preg_replace("#^\.\. code-block:: bash\n\n#m", '', $matches[0]);

                if ('php' !== $matches[1]) {
                    $result = preg_replace("#<\?{$matches[1]}\s*#", '', $result);
                }

                $result = preg_replace("#\n\n +\?>#", '', $result);

                return $result;
            },
            $help
        );
        $help = preg_replace('#^                        #m', '  ', $help);
        $help = preg_replace('#\*\* +\[#', '** [', $help);

        $output->write($header."\n".$help."\n");
    }
}
