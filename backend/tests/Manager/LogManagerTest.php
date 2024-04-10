<?php

namespace App\Tests\Manager;

use App\Manager\LogManager;
use App\Manager\ErrorManager;
use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LogManagerTest
 * 
 * Unit tests for the LogManager class.
 * 
 * @package App\Tests\Manager
 */
class LogManagerTest extends TestCase
{
    /**
     * Tests the log method of the LogManager class.
     *
     * This method creates mocks for ErrorManager, VisitorInfoUtil, and EntityManagerInterface.
     * It sets expectations for the method calls on these mocks.
     * Then, it creates an instance of LogManager with the mocks and calls the log method.
     * Finally, it asserts that the expected method calls were made on the mocks.
     */
    public function testLog(): void
    {
        // mock ErrorManager
        $errorManager = $this->createMock(ErrorManager::class);
        $errorManager->expects($this->never())->method('handleError');

        // mock VisitorInfoUtil
        $visitorInfoUtil = $this->createMock(VisitorInfoUtil::class);
        $visitorInfoUtil->method('getIP')->willReturn('192.168.1.1');

        // mock EntityManagerInterface
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        // create LogManager instance with mocks
        $logManager = new LogManager($errorManager, $visitorInfoUtil, $entityManager);

        // call the log method
        $logManager->log('Test Name', 'Test Message');
    }
}
