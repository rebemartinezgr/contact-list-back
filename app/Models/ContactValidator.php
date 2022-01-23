<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace App\Models;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Support\Facades\Validator;

class ContactValidator
{
    const FIRST_NAME_MAX_LENGTH = 30;
    const LAST_NAME_MAX_LENGTH = 30;
    const EMAIL_MAX_LENGTH = 50;
    const PHONE_MAX_LENGTH = 16;

    private array $fieldRules = [
        Contact::FIRST_NAME_FIELD => 'required|max:' . self::FIRST_NAME_MAX_LENGTH,
        Contact::LAST_NAME_FIELD => 'required|max:' .  self::LAST_NAME_MAX_LENGTH,
        Contact::EMAIL_FIELD => 'required|max:' . self::EMAIL_MAX_LENGTH . '|unique:contacts,email',
        Contact::PHONE_FIELD => 'required|max:' . self::PHONE_MAX_LENGTH
    ];

    const FIELD_LABEL_MAPPING = [
        Contact::FIRST_NAME_FIELD => 'first name',
        Contact::LAST_NAME_FIELD => 'last name',
        Contact::EMAIL_FIELD => 'email address',
        Contact::PHONE_FIELD => 'phone number',
    ];

    const ERROR_MESSAGES = [
        'required' => 'The :attribute field is required.',
        'max' => 'The :attribute size must be less than :max.',
        'unique' => 'The :attribute already exists',
    ];

    /**
     * @param Contact $contact
     * @return ValidationValidator
     */
    public function getValidator(Contact $contact): ValidationValidator
    {
        $values = $contact->toArray();
        $id = $contact->getAttribute(Contact::ENTITY_ID_FIELD);
        /* Ignore the current contact on unique email validation*/
        if ($id !== null) {
            $this->fieldRules[Contact::EMAIL_FIELD] .= ",{$id}";
        }

        return Validator::make(
            $values,
            $this->fieldRules,
            self::ERROR_MESSAGES,
            self::FIELD_LABEL_MAPPING
        );
    }
}
