<?php

namespace Kewlar\Composer\Tests;

use Composer\Package\CompletePackage;
use Kewlar\Composer\ChangelogsPlugin;
use Kewlar\Composer\Exception;

/**
 * Class ChangelogsPluginTest
 *
 * Contains unit tests for ChangelogsPlugin.
 *
 * @author Mindaugas Pelionis <mindaugas.pelionis@gmail.com>
 */
class ChangelogsPluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Checks if ChangelogsPlugin::getChangelog generates an expected result given two packages.
     *
     * @param array  $initialPackageConfig Configuration values for initial CompletePackage
     * @param array  $targetPackageConfig  Configuration values for target CompletePackage
     * @param string $compareUrl           Expected changelog URL.
     *
     * @dataProvider testGetChangelogProvider
     */
    public function testGetChangelog($initialPackageConfig, $targetPackageConfig, $compareUrl)
    {
        $initialPackage = ChangelogsPluginTest::getCompletePackageFromArray($initialPackageConfig);
        $targetPackage = ChangelogsPluginTest::getCompletePackageFromArray($targetPackageConfig);
        $this->assertEquals(
            $compareUrl,
            ChangelogsPlugin::getChangelog($initialPackage, $targetPackage)
        );
    }

    /**
     * Test data provider for testGetChangelog().
     *
     * @return array
     */
    public static function testGetChangelogProvider()
    {
        return [
            [
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.4.0',
                    'prettyVersion' => '1.0.4',
                    'sourceUrl' => 'https://github.com/doctrine/instantiator.git',
                ],
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.5.0',
                    'prettyVersion' => '1.0.5',
                    'sourceUrl' => 'https://github.com/doctrine/instantiator.git',
                ],
                'https://github.com/doctrine/instantiator/compare/1.0.4...1.0.5',
            ],
        ];
    }

    /**
     * Checks if ChangelogsPlugin::getChangelog generates an expected exception, if it cannot calculate the changelog
     * between two packages.
     *
     * @param array $initialPackageConfig  Configuration values for initial CompletePackage.
     * @param array $targetPackageConfig   Configuration values for target CompletePackage.
     * @param int   $expectedExceptionCode Expected exception code.
     *
     * @dataProvider testGetChangelogExceptionProvider
     */
    public function testGetChangelogException($initialPackageConfig, $targetPackageConfig, $expectedExceptionCode)
    {
        $initialPackage = ChangelogsPluginTest::getCompletePackageFromArray($initialPackageConfig);
        $targetPackage = ChangelogsPluginTest::getCompletePackageFromArray($targetPackageConfig);
        $this->setExpectedException('Kewlar\\Composer\\Exception\\CouldNotCalculateChangelog', '', $expectedExceptionCode);
        ChangelogsPlugin::getChangelog($initialPackage, $targetPackage);
    }

    /**
     * Test data provider for testGetChangelogException().
     *
     * @return array
     */
    public static function testGetChangelogExceptionProvider()
    {
        return [
            [
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.4.0',
                    'prettyVersion' => '1.0.4',
                    'sourceUrl' => 'https://hubgit.com/doctrine/instantiator.git',
                ],
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.5.0',
                    'prettyVersion' => '1.0.5',
                    'sourceUrl' => 'https://hubgit.com/doctrine/instantiator.git',
                ],
                Exception\CouldNotCalculateChangelog::CODE_SOURCEURL_UNSUPPORTED,
            ],
            [
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.4.0',
                    'prettyVersion' => '1.0.4',
                    'sourceUrl' => 'https://localhost/doctrine/instantiator.git',
                ],
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.5.0',
                    'prettyVersion' => '1.0.5',
                    'sourceUrl' => 'https://localhost/doctrine/instantiator.git',
                ],
                Exception\CouldNotCalculateChangelog::CODE_SOURCEURL_UNSUPPORTED,
            ],
            [
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.4.0',
                    'prettyVersion' => '1.0.4',
                    'sourceUrl' => 'https://github.com/doctrine/instantiator.git',
                ],
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.5.0',
                    'prettyVersion' => '1.0.5',
                    'sourceUrl' => 'https://github.com/doctrine2/instantiator.git',
                ],
                Exception\CouldNotCalculateChangelog::CODE_SOURCEURL_MISMATCH,
            ],
            [
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.4.0',
                    'prettyVersion' => '1.0.4',
                    'sourceUrl' => 'https://github.com/doctrine/instantiator.git',
                ],
                [
                    'name' => 'doctrine/instantiator',
                    'version' => '1.0.5.0',
                    'prettyVersion' => '1.0.5',
                    'sourceUrl' => 'https://github.com/doctrine/instantiator2.git',
                ],
                Exception\CouldNotCalculateChangelog::CODE_SOURCEURL_MISMATCH,
            ],
        ];
    }

    /**
     * Creates a new CompletePackage instance and populates it with values from $packageConfig.
     *
     * @param array $packageConfig Package configuration values.
     *
     * @return CompletePackage
     */
    private static function getCompletePackageFromArray($packageConfig)
    {
        $package = new CompletePackage(
            $packageConfig['name'],
            $packageConfig['version'],
            $packageConfig['prettyVersion']
        );
        $package->setSourceUrl($packageConfig['sourceUrl']);

        return $package;
    }
}
