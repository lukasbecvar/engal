<?php

namespace App\Tests\Auth;

use App\Entity\User;
use App\Util\SiteUtil;
use Symfony\Component\String\ByteString;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    private $client;

    private function createSiteUtilMock(bool $register_enabled): object
    {
        // create moc site util fake object
        $siteUtilMock = $this->createMock(SiteUtil::class);

        // init fake testing value
        $siteUtilMock->method('isRegisterEnabled')->willReturn($register_enabled);
        $siteUtilMock->method('isRunningLocalhost')->willReturn(true);

        return $siteUtilMock;
    }

    protected function setUp(): void
    {
        parent::setUp();
    
        // create client instance
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        $this->removeFakeData();
        parent::tearDown();
    }

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
