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
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶4.3, ¶4.6, ¶5.
 *
 * @author Marc Aubé
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class OrtParenthesisFixer extends AbstractFixer
{


    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the ElseIfFixer and DuplicateSemicolonFixer
        return -30;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals('(')) {
                continue;
            }

            $prevIndex = $tokens->getPrevMeaningfulToken($index);

            // ignore parenthesis for T_ARRAY
            if (null !== $prevIndex && $tokens[$prevIndex]->isGivenKind(T_ARRAY)) {
                continue;
            }

            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

            // add space before opening `(`
            $tokens->removeTrailingWhitespace($index,' ');
            $this->addSpaceAroundToken($tokens, $index, 1);

            // add space before closing `)` if it is not `list($a, $b, )` case
            if (!$tokens[$tokens->getPrevMeaningfulToken($endIndex)]->equals(',') && !$tokens[$tokens->getPrevMeaningfulToken($endIndex)]->equals('(')) {
                $this->addSpaceAroundToken($tokens, $endIndex, +1);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST be a space after the opening parenthesis. There MUST be a space before the closing parenthesis.';
    }

    /**
     * Remove spaces on one side of the token at a given index.
     *
     * @param Tokens $tokens A collection of code tokens
     * @param int    $index  The token index
     * @param int    $offset The offset where to start looking for spaces
     */
    private function addSpaceAroundToken(Tokens $tokens, $index, $offset)
    {
        if (!isset($tokens[$index + $offset])) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($index,$offset,' ');
    }
}
