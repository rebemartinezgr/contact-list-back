<?php
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace Tests\Unit;

use App\Models\Contact;
use App\Models\ContactValidator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;

class ContactModelTest extends TestCase
{

    private Contact $contact;

    private ContactValidator $validatorMock;

    private array $data = [
        Contact::FIRST_NAME_FIELD => 'first name test',
        Contact::LAST_NAME_FIELD => 'last name test',
        Contact::EMAIL_FIELD => 'email@dummy.com',
        Contact::PHONE_FIELD => '123456'
    ];

    protected function setUp(): void
    {
        $this->validatorMock = static::createMock(ContactValidator::class);
        $this->contact = new Contact($this->data, $this->validatorMock);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test class construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertEquals($this->data[Contact::FIRST_NAME_FIELD], $this->contact->getAttribute(Contact::FIRST_NAME_FIELD));
        $this->assertEquals($this->data[Contact::LAST_NAME_FIELD], $this->contact->getAttribute(Contact::LAST_NAME_FIELD));
        $this->assertEquals($this->data[Contact::EMAIL_FIELD], $this->contact->getAttribute(Contact::EMAIL_FIELD));
        $this->assertEquals($this->data[Contact::PHONE_FIELD], $this->contact->getAttribute(Contact::PHONE_FIELD));
    }

    /**
     * test contact validation
     *
     * @return void
     * @throws ValidationException
     */
    public function testValidate()
    {
        $validationValidatorMock = static::createMock(ValidationValidator::class);
        $validationValidatorMock->expects(static::once())->method('validate');
        $this->validatorMock
            ->expects(static::once())
            ->method('getValidator')
            ->with($this->contact)
            ->willReturn($validationValidatorMock);
        $this->contact->validate();
    }

    /**
     * test contact error validation
     *
     * @return void
     */
    public function testValidationError()
    {
        $validationValidatorMock = static::createMock(ValidationValidator::class);
        $validationValidatorMock
            ->expects(static::once())
            ->method('validate')
            ->willThrowException(new ValidationException($validationValidatorMock));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("The given data was invalid.");
        $this->validatorMock
            ->expects(static::once())
            ->method('getValidator')
            ->with($this->contact)
            ->willReturn($validationValidatorMock);
        $this->contact->validate();
    }
}
