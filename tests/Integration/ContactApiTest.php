<?php
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace Tests\Integration;

use App\Http\Controllers\ContactController;
use App\Models\Contact;
use App\Models\ContactValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\ContactSeeder;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    private Contact $contact;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ContactSeeder::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test GET api/contacts: contacts.index
     */
    public function testIndex()
    {
        $dataValue = Contact::get()->toArray();
        $expectedValue = [
            ContactController::RESPONSE_ERROR_KEY => [],
            ContactController::RESPONSE_MSG_KEY => '',
            ContactController::RESPONSE_DATA_KEY => $dataValue,
        ];
        $response = $this->json('get', "api/contacts");
        $response->assertOk()
            ->assertJsonStructure(
                [
                    ContactController::RESPONSE_ERROR_KEY,
                    ContactController::RESPONSE_MSG_KEY,
                    ContactController::RESPONSE_DATA_KEY => [
                        '*' => [
                            Contact::ENTITY_ID_FIELD,
                            Contact::FIRST_NAME_FIELD,
                            Contact::LAST_NAME_FIELD,
                            Contact::EMAIL_FIELD,
                            Contact::PHONE_FIELD,
                            Contact::CREATED_AT,
                            Contact::UPDATED_AT,
                            Contact::DELETED_AT
                        ]
                    ]
                ]
            )->assertJson($response->json())
            ->assertExactJson($expectedValue);;
    }

    /**
     * Test GET api/contacts/{contact}: contacts.show
     */
    public function testShow()
    {
        $existingContact = Contact::where('id', '>', 1)->first();
        $dataValue = array_merge(
            $existingContact->toArray(),
            [Contact::HISTORY_RELATION => $existingContact->getHistory()]
        );
        $expectedValue = [
            ContactController::RESPONSE_ERROR_KEY => [],
            ContactController::RESPONSE_MSG_KEY => '',
            ContactController::RESPONSE_DATA_KEY => $dataValue,
        ];
        $response = $this->json(
            'GET',
            "api/contacts/{$existingContact->getAttribute(Contact::ENTITY_ID_FIELD)}");
        $response->assertOk()
            ->assertJsonStructure(
                [
                    ContactController::RESPONSE_ERROR_KEY,
                    ContactController::RESPONSE_MSG_KEY,
                    ContactController::RESPONSE_DATA_KEY => [
                        'id',
                        Contact::FIRST_NAME_FIELD,
                        Contact::LAST_NAME_FIELD,
                        Contact::EMAIL_FIELD,
                        Contact::PHONE_FIELD,
                        Contact::CREATED_AT,
                        Contact::UPDATED_AT,
                        Contact::DELETED_AT,
                        Contact::HISTORY_RELATION,
                    ]
                ]
            )->assertJson($response->json())
            ->assertExactJson($expectedValue);
    }

    /**
     * Test GET api/contacts/{contact}: contacts.show
     */
    public function testShowForMissingContact()
    {
        $this->json('get', "api/contacts/0")
            ->assertNotFound();
    }

    /**
     * Test POST api/contacts: contacts.store
     */
    public function testStoreContacts()
    {
        $newData = [
            Contact::FIRST_NAME_FIELD => 'first name updated',
            Contact::LAST_NAME_FIELD => 'last name updated',
            Contact::EMAIL_FIELD => 'email.updated@dummy.com',
            Contact::PHONE_FIELD => '123456'];

        $response = $this->json('POST', "api/contacts", $newData);
        $response->assertCreated()
            ->assertJsonStructure(
                [
                    ContactController::RESPONSE_ERROR_KEY,
                    ContactController::RESPONSE_MSG_KEY,
                    ContactController::RESPONSE_DATA_KEY => [
                        'id',
                        Contact::FIRST_NAME_FIELD,
                        Contact::LAST_NAME_FIELD,
                        Contact::EMAIL_FIELD,
                        Contact::PHONE_FIELD,
                        Contact::CREATED_AT,
                        Contact::UPDATED_AT,
                        Contact::HISTORY_RELATION,
                    ]
                ]
            )->assertJson($response->json())
            ->assertJsonFragment([ContactController::RESPONSE_MSG_KEY => ContactController::CREATE_SUCCESS_MSG])
            ->assertJsonFragment([ContactController::RESPONSE_ERROR_KEY => []])
            ->assertJsonFragment([Contact::HISTORY_RELATION => []]);

        $response = $response->json();
        $contactResponse = $response[ContactController::RESPONSE_DATA_KEY];
        $this->assertEquals($newData[Contact::FIRST_NAME_FIELD], $contactResponse[Contact::FIRST_NAME_FIELD]);
        $this->assertEquals($newData[Contact::LAST_NAME_FIELD], $contactResponse[Contact::LAST_NAME_FIELD]);
        $this->assertEquals($newData[Contact::EMAIL_FIELD], $contactResponse[Contact::EMAIL_FIELD]);
        $this->assertEquals($newData[Contact::PHONE_FIELD], $contactResponse[Contact::PHONE_FIELD]);
        $this->assertNotNull($contactResponse[Contact::CREATED_AT]);
    }

    /**
     * Test POST api/contacts: contacts.store
     */
    public function testStoreWithExistingEmail()
    {
        $newData = [
            Contact::FIRST_NAME_FIELD => 'first name updated',
            Contact::LAST_NAME_FIELD => 'last name updated',
            Contact::EMAIL_FIELD => 'email.updated@dummy.com',
            Contact::PHONE_FIELD => '123456'];

        $this->json('POST', "api/contacts", $newData);
        $response = $this->json('POST', "api/contacts", $newData);
        $response->assertUnprocessable()
            ->assertJsonStructure(
                [
                    ContactController::RESPONSE_ERROR_KEY,
                    ContactController::RESPONSE_MSG_KEY,
                    ContactController::RESPONSE_DATA_KEY
                ]
            )->assertJson($response->json())
            ->assertJsonFragment([ContactController::RESPONSE_MSG_KEY => ContactController::VALIDATION_ERROR])
            ->assertJsonFragment([ContactController::RESPONSE_ERROR_KEY => [
                    str_replace(':attribute', ContactValidator::FIELD_LABEL_MAPPING[Contact::EMAIL_FIELD], ContactValidator::ERROR_MESSAGES['unique'])
                ]]
            )->assertJsonFragment([ContactController::RESPONSE_DATA_KEY => []])
            ->assertJsonCount(1, ContactController::RESPONSE_ERROR_KEY);
    }

    /**
     * Test PUT api/contacts/{contact}: contacts.update
     */
    public function testUpdateContact()
    {
        $existingContact = Contact::where('id', '>', 1)->first();
        $newData = [
            Contact::FIRST_NAME_FIELD => 'first name updated',
            Contact::LAST_NAME_FIELD => 'last name updated',
            Contact::EMAIL_FIELD => 'email.updated@dummy.com',
            Contact::PHONE_FIELD => '123456'];

        $response = $this->json('PUT', "api/contacts/{$existingContact->getAttribute(Contact::ENTITY_ID_FIELD)}", $newData);
        $response->assertOk()
            ->assertJsonStructure(
                [
                    ContactController::RESPONSE_ERROR_KEY,
                    ContactController::RESPONSE_MSG_KEY,
                    ContactController::RESPONSE_DATA_KEY => [
                        'id',
                        Contact::FIRST_NAME_FIELD,
                        Contact::LAST_NAME_FIELD,
                        Contact::EMAIL_FIELD,
                        Contact::PHONE_FIELD,
                        Contact::CREATED_AT,
                        Contact::UPDATED_AT,
                        Contact::DELETED_AT,
                        Contact::HISTORY_RELATION,
                    ]
                ]
            )->assertJson($response->json())
            ->assertJsonFragment([ContactController::RESPONSE_MSG_KEY => ContactController::UPDATE_SUCCESS_MSG])
            ->assertJsonFragment([ContactController::RESPONSE_ERROR_KEY => []]);

        $response = $response->json();
        $contactResponse = $response[ContactController::RESPONSE_DATA_KEY];
        $historyResponse = $contactResponse[Contact::HISTORY_RELATION];
        $oldData = $existingContact->toArray();
        $this->assertEquals($newData[Contact::FIRST_NAME_FIELD], $contactResponse[Contact::FIRST_NAME_FIELD]);
        $this->assertEquals($newData[Contact::LAST_NAME_FIELD], $contactResponse[Contact::LAST_NAME_FIELD]);
        $this->assertEquals($newData[Contact::EMAIL_FIELD], $contactResponse[Contact::EMAIL_FIELD]);
        $this->assertEquals($newData[Contact::PHONE_FIELD], $contactResponse[Contact::PHONE_FIELD]);
        $this->assertNotNull($contactResponse[Contact::UPDATED_AT]);
        $this->assertEquals($oldData[Contact::FIRST_NAME_FIELD], $historyResponse[0][Contact::FIRST_NAME_FIELD]);
        $this->assertEquals($oldData[Contact::LAST_NAME_FIELD], $historyResponse[0][Contact::LAST_NAME_FIELD]);
        $this->assertEquals($oldData[Contact::EMAIL_FIELD], $historyResponse[0][Contact::EMAIL_FIELD]);
        $this->assertEquals($oldData[Contact::PHONE_FIELD], $historyResponse[0][Contact::PHONE_FIELD]);
    }

    /**
     * Test PUT api/contacts/{contact}: contacts.update
     */
    public function testUpdateForMissingContact()
    {
        $response = $this->json('PUT', "api/contacts/0", []);
        $response->assertNotFound();
    }

    /**
     * Test PUT api/contacts/{contact}: contacts.update
     */
    public function testUpdateWithInvalidRequiredData()
    {
        $existingContact = Contact::where('id', '>', 1)->first();
        $invalidEmptyData = [
            Contact::FIRST_NAME_FIELD => '',
            Contact::LAST_NAME_FIELD => '',
            Contact::EMAIL_FIELD => '',
            Contact::PHONE_FIELD => ''
        ];

        $response = $this->json(
            'PUT',
            "api/contacts/{$existingContact->getAttribute(Contact::ENTITY_ID_FIELD)}", $invalidEmptyData);
        $response->assertUnprocessable()
            ->assertJsonStructure(
                [
                    ContactController::RESPONSE_ERROR_KEY,
                    ContactController::RESPONSE_MSG_KEY,
                    ContactController::RESPONSE_DATA_KEY
                ]
            )->assertJson($response->json())
            ->assertJsonFragment([ContactController::RESPONSE_MSG_KEY => ContactController::VALIDATION_ERROR])
            ->assertJsonFragment([ContactController::RESPONSE_DATA_KEY => []])
            ->assertJsonCount(count($invalidEmptyData), ContactController::RESPONSE_ERROR_KEY);
    }

    /**
     * Test DELETE api/contacts/{contact}: contacts.destroy
     */
    public function testDestroyContact()
    {
        $existingContact = Contact::where('id', '>', 1)->first();
        $response = $this->json(
            'DELETE',
            "api/contacts/{$existingContact->getAttribute(Contact::ENTITY_ID_FIELD)}");
        $expectedValue = [
            ContactController::RESPONSE_ERROR_KEY => [],
            ContactController::RESPONSE_MSG_KEY => ContactController::DELETE_SUCCESS_MSG,
            ContactController::RESPONSE_DATA_KEY => [],
        ];
        $response
            ->assertOk()
            ->assertJsonStructure(
                [
                    ContactController::RESPONSE_ERROR_KEY,
                    ContactController::RESPONSE_MSG_KEY,
                    ContactController::RESPONSE_DATA_KEY => []
                ]
            )->assertJson($response->json())
            ->assertExactJson($expectedValue);

        $response = $this->json(
            'DELETE',
            "api/contacts/{$existingContact->getAttribute(Contact::ENTITY_ID_FIELD)}");
        $response->assertNotFound();
    }

    /**
     * Test DELETE api/contacts/{contact}: contacts.destroy
     */
    public function testDestroyForMissingContact()
    {
        $response = $this->json('delete', "api/contacts/0");
        $response->assertNotFound();
    }
}
