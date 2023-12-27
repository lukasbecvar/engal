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

    /**
     * Extracts and returns an array of integers from a given string.
     *
     * This method uses regular expression matching to find all numeric sequences
     * within the input string and returns them as an array of integers.
     *
     * @param string $str The input string from which to extract numeric sequences.
     *
     * @return array An array of integers extracted from the input string.
     */
    public function getNumbers(string $str): array
    {
        preg_match_all('/\d+/', $str, $matches);
        return array_map('intval', $matches[0]);
    }
}
