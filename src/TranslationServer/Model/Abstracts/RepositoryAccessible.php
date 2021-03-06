<?php

/*
 * This file is part of the translation-server package
 *
 * Copyright (c) 2015 Marc Morera
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\TranslationServer\Model\Abstracts;

use Mmoreram\TranslationServer\Model\Translation;

/**
 * Class RepositoryAccessible
 */
abstract class RepositoryAccessible extends TranslationAccessible
{
    /**
     * Get translations
     *
     * @param array $domains   Domains
     * @param array $languages Languages
     *
     * @return Translation[] $translations Set of translations
     */
    abstract public function getRepositories(
        array $domains = [],
        array $languages = []
    );
}
