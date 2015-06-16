<?php

namespace Kewlar\Composer\Exception;

/**
 * Class Exception\CouldNotCalculateChangelog
 *
 * An exception, thrown when ChangelogsPlugin cannot determine the changelog between two packages.
 *
 * @author Mindaugas Pelionis <mindaugas.pelionis@gmail.com>
 */
class CouldNotCalculateChangelog extends \Exception
{
    // ChangelogsPlugin knows only how to link to GitHub "Compare" pages. Others types of repos are unsupported.
    const CODE_SOURCEURL_UNSUPPORTED = 1;

    // Package source URLs must belong to the same repo.
    const CODE_SOURCEURL_MISMATCH = 2;
}
