<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * Obtain information about using version of tool.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ToolInfo
{
    const COMPOSER_INSTALLED_FILE = '/../../composer/installed.json';
    const COMPOSER_PACKAGE_NAME = 'styleci/php-cs-fixer';

    public static function getVersion()
    {
        static $result;

        if (null === $result) {
            if (file_exists($path = self::getScriptDir().self::COMPOSER_INSTALLED_FILE)) {
                foreach (json_decode(file_get_contents($path), true) as $package) {
                    if (self::COMPOSER_PACKAGE_NAME === $package['name']) {
                        $result = $package['version'].'#'.$package['dist']['reference'];
                        break;
                    }
                }
            }
        }

        return $result ?: '';
    }

    private static function getScriptDir()
    {
        static $result;

        if (null === $result) {
            $script = $_SERVER['SCRIPT_NAME'];

            if (is_link($script)) {
                $linkTarget = readlink($script);

                // If the link target is relative to the link
                if (false === realpath($linkTarget)) {
                    $linkTarget = dirname($script).'/'.$linkTarget;
                }

                $script = $linkTarget;
            }

            $result = dirname($script);

            if ('.' === $result) {
                $result = realpath(__DIR__.'/../../');
            }
        }

        return $result;
    }
}
