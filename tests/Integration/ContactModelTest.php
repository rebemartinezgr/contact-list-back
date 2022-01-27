<?php
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace Tests\Integration;

use App\Models\Contact;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactModelTest extends TestCase
{
    use RefreshDatabase;

    private Contact $contact;

    private array $data = [
        Contact::FIRST_NAME_FIELD => 'first name test',
        Contact::LAST_NAME_FIELD => 'last name test',
        Contact::EMAIL_FIELD => 'email@dummy.com',
        Contact::PHONE_FIELD => '123456'
    ];

    protected function setUp(): void
    {
        $this->contact = new Contact($this->data);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test contact model saving on database
     *
     * @throws \Exception
     */
    public function testSaveOnCreation()
    {
        // Check if contact has been saved in database
        $this->contact->save();
        $this->assertDatabaseHas($this->contact->getTable(), $this->data);
    }

    /**
     * Test contact model updating on database
     *
     * @throws \Exception
     */
    public function testSaveOnUpdate()
    {
        $this->contact->save();
        // Check if contact has been updated in database
        $contact = Contact::where(Contact::EMAIL_FIELD, '=', $this->data[Contact::EMAIL_FIELD])->firstOrFail();
        $dataToUpdate = [
            Contact::FIRST_NAME_FIELD => 'first name updated',
            Contact::LAST_NAME_FIELD => 'last name updated',
            Contact::EMAIL_FIELD => 'emailupdated@dummy.com',
            Contact::PHONE_FIELD => '99999999'
        ];
        $contact->setAttribute(Contact::FIRST_NAME_FIELD, $dataToUpdate[Contact::FIRST_NAME_FIELD]);
        $contact->setAttribute(Contact::LAST_NAME_FIELD, $dataToUpdate[Contact::LAST_NAME_FIELD]);
        $contact->setAttribute(Contact::PHONE_FIELD, $dataToUpdate[Contact::PHONE_FIELD]);
        $contact->setAttribute(Contact::EMAIL_FIELD, $dataToUpdate[Contact::EMAIL_FIELD]);
        $contact->save();
        $this->assertDatabaseHas($contact->getTable(), $dataToUpdate);

        // Check if history relationship has been saved in database
        $historyData = array_merge($this->data, ['contact_id' => $contact->getAttribute('id')]);
        $this->assertDatabaseHas('histories', $historyData);
    }

    /**
     * Test contact model saving with duplicate key error
     */
    public function testSaveErrorDuplicateKey()
    {
        $this->contact->save();
        $this->expectException(Exception::class);
        $newContact = new Contact($this->data);
        $newContact->save();
    }
}
