<?php

namespace App\Tests\UserBundle;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use UserBundle\Entity\User;

final class UserTest extends TestCase
{

  public function testMockObjectRepositoryAndObjectManager()
  {
    $user = new User();

    // Now, mock the repository so it retures the mock of the employee
    /**
     * @var MockObject
     */
    $userRepo = $this->createMock(ObjectRepository::class);
    $userRepo->expects($this->any())
      ->method('find')
      ->willReturn($user);

    /**
     * @var MockObject
     */
    $objectManager = $this->createMock(ObjectManager::class);
    $objectManager->expects($this->any())
      ->method('getRepository')
      ->willReturn($userRepo);

    $this->assertTrue(true);
  }
}
