<?php

namespace Kewlar\Composer;

use Composer\Composer;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\InstallerEvent;
use Composer\Installer\InstallerEvents;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Kewlar\Composer\Exception;

/**
 * Class ChangelogsPlugin
 *
 * A composer plugin for `composer update` that prints links to updated packages' GitHub compare pages
 * for easier access to package changelogs.
 *
 * @author Mindaugas Pelionis <mindaugas.pelionis@gmail.com>
 */
class ChangelogsPlugin implements PluginInterface, EventSubscriberInterface
{
    const PAD_STR = '    ';

    /** @type Composer */
    protected $composer;
    /** @type IOInterface */
    protected $io;

    /**
     * @inheritdoc
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            // Only here because it actually fires when doing `composer update --dry-run`.
            InstallerEvents::POST_DEPENDENCIES_SOLVING => 'onPostDependenciesSolving',

            // POST_PACKAGE_UPDATE event would be perfect, but, sadly, it does not fire when doing `--dry-run`.
            PackageEvents::POST_PACKAGE_UPDATE => 'onPostPackageUpdate',
        ];
    }

    public function onPostDependenciesSolving(InstallerEvent $event)
    {
        $changelogs = [];
        $operations = $event->getOperations();
        foreach ($operations as $operation) {
            if ($operation instanceof UpdateOperation) {
                try {
                    $changelogs[] = self::getChangelog($operation->getInitialPackage(), $operation->getTargetPackage());
                } catch (Exception\CouldNotCalculateChangelog $e) {
                    $changelogs[] = $e->getMessage();
                }
            }
        }

        if (!empty($changelogs)) {
            $this->io->write(self::PAD_STR . 'CHANGELOGS:');
            foreach ($changelogs as $changelog) {
                $this->io->write(self::PAD_STR . self::PAD_STR . $changelog);
            }
        }
    }

    public function onPostPackageUpdate(PackageEvent $event)
    {
        $operation = $event->getOperation();
        if ($operation instanceof UpdateOperation) {
            try {
                $changelog = self::getChangelog($operation->getInitialPackage(), $operation->getTargetPackage());
            } catch (Exception\CouldNotCalculateChangelog $e) {
                $changelog = $e->getMessage();
            }
            $this->io->write(self::PAD_STR . 'CHANGELOG: ' . $changelog);
        }
    }

    /**
     * Returns a GitHub "Comparing changes" URL for provided package versions.
     *
     * @param PackageInterface $initialPackage
     * @param PackageInterface $targetPackage
     *
     * @throws Exception\CouldNotCalculateChangelog
     *
     * @return string
     */
    public static function getChangelog(PackageInterface $initialPackage, PackageInterface $targetPackage)
    {
        if ($initialPackage->getSourceUrl() === $targetPackage->getSourceUrl()) {
            if (preg_match('/^https?:\\/\\/github\\.com\\/[^\\/]+\\/[^\\/]+\\.git$/', $initialPackage->getSourceUrl())) {
                // Example:
                // PackageInterface::sourceUrl: https://github.com/sonata-project/SonataCoreBundle.git
                // PackageInterface::prettyVersion: 2.2 (or 2.4, or master)
                // Result: https://github.com/sonata-project/SonataCoreBundle/compare/2.2...master
                return preg_replace(
                        '/\\.git$/',
                        '/compare/' . $initialPackage->getPrettyVersion() . '...' . $targetPackage->getPrettyVersion(),
                        $targetPackage->getSourceUrl()
                    );
            }

            throw new Exception\CouldNotCalculateChangelog(
                'Unknown changelog; not a GitHub URL: ' . $initialPackage->getSourceUrl(),
                Exception\CouldNotCalculateChangelog::CODE_SOURCEURL_UNSUPPORTED
            );
        }

        throw new Exception\CouldNotCalculateChangelog(
            'Unknown changelog; source URLs don\'t match: ' .
                $initialPackage->getSourceUrl() . ', ' . $targetPackage->getSourceUrl(),
            Exception\CouldNotCalculateChangelog::CODE_SOURCEURL_MISMATCH
        );
    }
}
