<?php
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace Tests\Unit;

use App\Models\Contact;
use App\Repositories\ContactRepository;
use Tests\TestCase;

class ContactRepositoryTest extends TestCase
{
    private Contact $contactMock;

    private ContactRepository $repository;

    private array $data = [
        Contact::FIRST_NAME_FIELD => 'first name test',
        Contact::LAST_NAME_FIELD => 'last name test',
        Contact::EMAIL_FIELD => 'email@dummy.com',
        Contact::PHONE_FIELD => '123456'
    ];

    protected function setUp(): void
    {
        $this->repository = new ContactRepository;
        $this->contactMock = static::createMock(Contact::class);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test delete contact
     */
    public function testDeleteContact()
    {
        $this->contactMock
            ->expects(static::once())
            ->method('delete');
        $this->repository->deleteContact($this->contactMock);
    }

    /**
     * Test save contact
     */
    public function testSaveContact()
    {
        $this->contactMock
            ->expects(static::once())
            ->method('fill')
            ->with($this->data);

        $this->contactMock
            ->expects(static::once())
            ->method('validate');

        $this->contactMock
            ->expects(static::once())
            ->method('save');

        $this->repository->saveContact($this->contactMock, $this->data);

    }
}
