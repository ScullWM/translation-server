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

namespace Mmoreram\TranslationServer\Command;

use Exception;
use Mmoreram\TranslationServer\Command\Abstracts\AbstractTranslationServerCommand;
use Mmoreram\TranslationServer\Model\Translation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class DeadCommand
 */
class AddCommand extends AbstractTranslationServerCommand
{
    /**
     * @var array
     */
    private $missingTranslation = [];

    /**
     * @var string
     */
    private $path;

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('translation:server:dead')
            ->setDescription('I See dead translations')
            ->addOption(
                '--path',
                '-p',
                InputOption::VALUE_NONE,
                "Search path",
                'src/'
            )
        ;

        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     *
     * @return int|null|void
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommand($output, false);
        $domains    = $input->getOption('domain');
        $this->path = $input->getOption('path');
        $project    = $this->createProject($input);
        $masterLanguage = $project->getMasterLanguage();

        $translations = $project->getTranslations($domains, [$masterLanguage]);

        foreach ($translations as $translation) {
            $this->checkDeadTranslations($translation);
        }

        $this->dumpFiles($this->missingTranslation, $masterLanguage, $project->getExportPath());

        $this->finishCommand($output);
    }

    /**
     * @param  Translation $translation
     */
    protected function checkDeadTranslations(Translation $translation)
    {
        $process = new Process('grep -r "' . $translation->getKey() . '" ' . $this->path);
        $process->run();

        if (!strstr($process->getOutput(), $translation->getKey())) {
            $this->missingTranslation[] = $translation->getKey();
        }
    }

    /**
     * @param  array  $missingTranslationsPerLanguage
     * @param  string $language
     * @param  string $exportPath
     */
    protected function dumpFiles($missingTranslationsPerLanguage, $language, $exportPath)
    {
        file_put_contents($exportPath . DIRECTORY_SEPARATOR. 'dead.' . $language .'.json', json_encode($missingTranslationsPerLanguage));
    }
}
