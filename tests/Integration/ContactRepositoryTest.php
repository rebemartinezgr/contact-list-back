<?php
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace Tests\Integration;

use App\Models\Contact;
use App\Repositories\ContactRepository;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContactRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private Contact $contact;

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
        $contact = new Contact($this->data);
        $contact->save();
        $this->repository->deleteContact($contact);
        $this->assertSoftDeleted($contact->getTable(), $this->data);
    }

    /**
     * Test save contact
     */
    public function testSaveContact()
    {
        $contact = new Contact();
        $this->repository->saveContact(new Contact, $this->data);
        $this->assertDatabaseHas($contact->getTable(), $this->data);
    }

    /**
     * Test get contact by contact
     */
    public function testGetContactById()
    {
        $contact = new Contact($this->data);
        $contact->save();
        $retrievedContact = $this->repository->getContactById($contact->getAttribute(Contact::ENTITY_ID_FIELD));
        $this->assertEquals($this->data[Contact::FIRST_NAME_FIELD], $retrievedContact->getAttribute(Contact::FIRST_NAME_FIELD));
        $this->assertEquals($this->data[Contact::LAST_NAME_FIELD], $retrievedContact->getAttribute(Contact::LAST_NAME_FIELD));
        $this->assertEquals($this->data[Contact::EMAIL_FIELD], $retrievedContact->getAttribute(Contact::EMAIL_FIELD));
        $this->assertEquals($this->data[Contact::PHONE_FIELD], $retrievedContact->getAttribute(Contact::PHONE_FIELD));
    }

    /**
     * Test get all contacts
     */
    public function testGetAllContacts()
    {
        $this->seed(ContactSeeder::class);
        $countContactsFromDataBase = DB::table(app(Contact::class)->getTable())
            ->where(Contact::DELETED_AT, null)
            ->count();
        $contacts = $this->repository->getAllContacts();
        $this->assertIsArray($contacts);
        $this->assertCount($countContactsFromDataBase, $contacts);
    }
}
