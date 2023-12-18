<?php

namespace App\Tests\Auth;

use App\Entity\User;
use App\Util\SiteUtil;
use Symfony\Component\String\ByteString;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RegisterTest
 * @package App\Tests\Auth
 */
class RegisterTest extends WebTestCase
{
    /**
     * @var mixed
     */
    private $client;

    /**
     * Creates a mock SiteUtil object for testing with a specified registration status.
     *
     * @param bool $register_enabled
     * @return object
     */
    private function createSiteUtilMock(bool $register_enabled): object
    {
        // create moc site util fake object
        $siteUtilMock = $this->createMock(SiteUtil::class);

        // init fake testing value
        $siteUtilMock->method('isRegisterEnabled')->willReturn($register_enabled);
        $siteUtilMock->method('isRunningLocalhost')->willReturn(true);

        return $siteUtilMock;
    }

    /**
     * Set up test data and environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    
        // create client instance
        $this->client = static::createClient();
    }

    /**
     * Tear down test data and environment.
     */
    protected function tearDown(): void
    {
        $this->removeFakeData();
        parent::tearDown();
    }

    /**
     * Remove fake user data after test execution.
     */
    private function removeFakeData(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $entityManager->getRepository(User::class);
        $fakeUser = $userRepository->findOneBy(['username' => 'testing_username']);

        // check if user exist
        if ($fakeUser) {
            $id = $fakeUser->getId();

            $entityManager->remove($fakeUser);
            $entityManager->flush();

            // reset auto-increment values for the users table
            $connection = $entityManager->getConnection();
            $connection->executeStatement("ALTER TABLE users AUTO_INCREMENT = " . ($id - 1));
        }
    }

    /**
     * Test for registration when it is disabled.
     */
    public function testRegisterDisabled(): void
    {
        // use fake site util instance
        $this->client->getContainer()->set(SiteUtil::class, $this->createSiteUtilMock(false));

        // make post request
        $this->client->request('POST', '/register');

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 403);
        $this->assertSame($data['message'], 'Registration is disabled');
    }

    /**
     * Test for registration with an empty username.
     */
    public function testRegisterEmptyUsername(): void
    {
        // use fake site util instance
        $this->client->getContainer()->set(SiteUtil::class, $this->createSiteUtilMock(true));

        // make post request
        $this->client->request('POST', '/register');

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'Required post data: username');
    }

    /**
     * Test for registration with an empty password.
     */
    public function testRegisterEmptyPassword(): void
    {
        // use fake site util instance
        $this->client->getContainer()->set(SiteUtil::class, $this->createSiteUtilMock(true));

        // make post request
        $this->client->request('POST', '/register', [
            'username' => ByteString::fromRandom(12)->toString()
        ]);

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'Required post data: password');
    }

    /**
     * Test for registration with an empty re-password.
     */
    public function testRegisterEmptyRePassword(): void
    {
        // use fake site util instance
        $this->client->getContainer()->set(SiteUtil::class, $this->createSiteUtilMock(true));

        // make post request
        $this->client->request('POST', '/register', [
            'username' => ByteString::fromRandom(12)->toString(),
            'password' => 'testing_password'
        ]);

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'Required post data: re-password');
    }

    /**
     * Test for registration with non-matching passwords.
     */
    public function testRegisterNotMatchedPasswords(): void
    {
        // use fake site util instance
        $this->client->getContainer()->set(SiteUtil::class, $this->createSiteUtilMock(true));

        // make post request
        $this->client->request('POST', '/register', [
            'username' => ByteString::fromRandom(12)->toString(),
            'password' => 'testing_password_1',
            're-password' => 'testing_password_2'
        ]);

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'error');
        $this->assertSame($data['code'], 400);
        $this->assertSame($data['message'], 'Passwords not matching');
    }

    /**
     * Test for a valid registration.
     */
    public function testRegisterValid(): void
    {
        // use fake site util instance
        $this->client->getContainer()->set(SiteUtil::class, $this->createSiteUtilMock(true));

        // make post request
        $this->client->request('POST', '/register', [
            'username' => 'testing_username',
            'password' => 'testing_password_1',
            're-password' => 'testing_password_1'
        ]);

        // get JSON content from the response
        $content = $this->client->getResponse()->getContent();

        // decode JSON content
        $data = json_decode($content, true);

        // test response code
        $this->assertResponseStatusCodeSame(200);

        // test response data
        $this->assertSame($data['status'], 'success');
        $this->assertSame($data['code'], 200);
        $this->assertSame($data['message'], 'User: testing_username registered successfully');
    }
}
