<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\StdinFileInfo;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Bram Gotink <bram@gotink.me>
 * @author Graham Campbell <graham@mineuk.com>
 */
final class Psr4Fixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $namespace = false;
        $classyName = null;
        $classyIndex = 0;

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_NAMESPACE)) {
                if (false !== $namespace) {
                    return;
                }

                $namespace = true;
            } elseif ($token->isClassy()) {
                if (null !== $classyName) {
                    return;
                }

                $classyIndex = $tokens->getNextMeaningfulToken($index);
                $classyName = $tokens[$classyIndex]->getContent();
            }
        }

        if (null === $classyName) {
            return;
        }

        if (false !== $namespace) {
            $filename = basename(strtr($file->getRealPath(), '\\', '/'), '.php');

            if ($classyName !== $filename) {
                $tokens[$classyIndex]->setContent($filename);
            }
        } else {
            $normClass = strtr($classyName, '_', '/');
            $filename = substr(strtr($file->getRealPath(), '\\', '/'), -strlen($normClass) - 4, -4);

            if ($normClass !== $filename && strtolower($normClass) === strtolower($filename)) {
                $tokens[$classyIndex]->setContent(strtr($filename, '/', '_'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Class names should match the file name.';
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        if ($file instanceof StdinFileInfo) {
            return false;
        }

        $filenameParts = explode('.', $file->getBasename(), 2);

        if (!isset($filenameParts[1]) || 'php' !== $filenameParts[1]) {
            return false;
        }

        // ignore stubs/fixtures, since they are typically containing invalid files for various reasons
        return !preg_match('{[/\\\\](stub|fixture)s?[/\\\\]}i', $file->getRealPath());
    }
}
