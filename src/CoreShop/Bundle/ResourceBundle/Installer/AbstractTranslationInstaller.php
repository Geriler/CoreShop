<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Installer;

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\TranslationConfiguration;
use Pimcore\Model\Translation;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractTranslationInstaller implements ResourceInstallerInterface
{
    protected $kernel;
    protected $translationType;

    public function __construct(KernelInterface $kernel, string $translationType = Translation::DOMAIN_DEFAULT)
    {
        $this->kernel = $kernel;
        $this->translationType = $translationType;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, string $applicationName = null, array $options = []): void
    {
        $parameter = $this->getIdentifier($applicationName);

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $translationFilesToInstall = $this->kernel->getContainer()->getParameter($parameter);
            $translationsToInstall = [];

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $processor = new Processor();
            $configurationDefinition = new TranslationConfiguration();

            foreach ($translationFilesToInstall as $file) {
                $file = $this->kernel->locateResource($file);

                if (file_exists($file)) {
                    $translations = Yaml::parse(file_get_contents($file));
                    $translations = $processor->processConfiguration($configurationDefinition, ['translations' => $translations]);
                    $translations = $translations['translations'];

                    foreach ($translations as $name => $translationData) {
                        $translationsToInstall[$name] = $translationData;
                    }
                }
            }

            $progress->start(count($translationsToInstall));

            foreach ($translationsToInstall as $name => $translationData) {
                $progress->setMessage(sprintf('Install %s Translation %s', $this->translationType, $name));

                $this->installTranslation($name, $translationData);

                $progress->advance();
            }

            $progress->finish();
            $progress->clear();

            $output->writeln(sprintf('  - <info>Translations have been installed successfully</info>'));
        }
    }

    abstract protected function getIdentifier(?string $applicationName = null): string;

    private function installTranslation(string $name, array $properties): Translation
    {
        /** @var Translation $translation */
        $translation = Translation::getByKey($name, $this->translationType, true);
        $translationData = $translation->getTranslations();
        $coreShopTranslationData = $properties['languages'];

        //no data found. set translation.
        if (empty($translationData)) {
            $translation->setTranslations($coreShopTranslationData);
        //there are already some translations. only update empty ones!
        } else {
            $mergedData = array_merge($coreShopTranslationData, array_filter($translationData));
            $translation->setTranslations($mergedData);
        }

        $translation->save();

        return $translation;
    }
}
