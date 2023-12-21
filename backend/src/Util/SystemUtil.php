<?php

namespace App\Util;

use App\Manager\ErrorManager;

/**
 * Class SystemUtil
 * @package App\Util
 */
class SystemUtil
{
    /** @var ErrorManager */
    private ErrorManager $errorManager;

    /**
     * SystemUtil constructor.
     *
     * @param ErrorManager $errorManager The error manager.
     */
    public function __construct(
        ErrorManager $errorManager,
    ) {
        $this->errorManager = $errorManager;
    }

    /**
     * Get the drive usage percentage.
     *
     * @return string|null The drive usage percentage or null on error.
     */
    public function getDriveUsage(): ?string 
    {
        try {
            return exec("df -Ph / | awk 'NR == 2{print $5}' | tr -d '%'");
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get drive usage: '.$e->getMessage(), 500);
            return null;
        }
    }
}
