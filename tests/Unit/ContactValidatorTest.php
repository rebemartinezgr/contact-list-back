<?php
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace Tests\Unit;

use App\Models\Contact;
use App\Models\ContactValidator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContactValidatorTest extends TestCase
{
    private Contact $contact;

    private ContactValidator $validator;

    private array $data = [
        Contact::FIRST_NAME_FIELD => 'first name test',
        Contact::LAST_NAME_FIELD => 'last name test',
        Contact::EMAIL_FIELD => 'email@dummy.com',
        Contact::PHONE_FIELD => '123456'
    ];

    protected function setUp(): void
    {
        $this->contact = new Contact($this->data);
        $this->validator = new ContactValidator();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test success validator validate
     *
     * @return void
     * @throws ValidationException
     */
    public function testSuccessValidate()
    {
        $validator = $this->validator->getValidator($this->contact);
        $validatedFields = $validator->validate();

        $this->assertArrayHasKey(Contact::FIRST_NAME_FIELD, $validatedFields);
        $this->assertArrayHasKey(Contact::LAST_NAME_FIELD, $validatedFields);
        $this->assertArrayHasKey(Contact::PHONE_FIELD, $validatedFields);
        $this->assertArrayHasKey(Contact::EMAIL_FIELD, $validatedFields);
    }

    /**
     * test required field rules
     *
     * @return void
     */
    public function testRequiredFieldsRule()
    {
        try{
            // validate should throw ValidationException when required field are empty is not valid
            $this->contact->setAttribute(Contact::FIRST_NAME_FIELD, '');
            $this->contact->setAttribute(Contact::LAST_NAME_FIELD, '');
            $this->contact->setAttribute(Contact::PHONE_FIELD, '');
            $this->contact->setAttribute(Contact::EMAIL_FIELD, '');
            $this->contact->validate();
            $this->expectException(ValidationException::class);
            $this->fail("Expected ValidationException not thrown");
        }catch(ValidationException $e) {
            $failedFields = $e->validator->failed();
            $this->assertArrayHasKey(Contact::FIRST_NAME_FIELD, $failedFields);
            $this->assertArrayHasKey(Contact::LAST_NAME_FIELD, $failedFields);
            $this->assertArrayHasKey(Contact::PHONE_FIELD, $failedFields);
            $this->assertArrayHasKey(Contact::EMAIL_FIELD, $failedFields);
        }
    }

    /**
     * test max length field rules
     *
     * @return void
     */
    public function testMaxLengthFieldsRule()
    {
        try{
            // validate should throw ValidationException when field are longer than allowed
            $this->contact->setAttribute(Contact::FIRST_NAME_FIELD, str_repeat("a", ContactValidator::FIRST_NAME_MAX_LENGTH+1));
            $this->contact->setAttribute(Contact::LAST_NAME_FIELD, str_repeat("a", ContactValidator::LAST_NAME_MAX_LENGTH+1));
            $this->contact->setAttribute(Contact::PHONE_FIELD, str_repeat("a", ContactValidator::PHONE_MAX_LENGTH+1));
            $this->contact->setAttribute(Contact::EMAIL_FIELD, str_repeat("a", ContactValidator::EMAIL_MAX_LENGTH+1));
            $this->contact->validate();
            $this->expectException(ValidationException::class);
            $this->fail("Expected ValidationException not thrown");
        }catch(ValidationException $e) {
            $failedFields = $e->validator->failed();
            $this->assertArrayHasKey(Contact::FIRST_NAME_FIELD, $failedFields);
            $this->assertArrayHasKey(Contact::LAST_NAME_FIELD, $failedFields);
            $this->assertArrayHasKey(Contact::PHONE_FIELD, $failedFields);
            $this->assertArrayHasKey(Contact::EMAIL_FIELD, $failedFields);
        }
    }
}
