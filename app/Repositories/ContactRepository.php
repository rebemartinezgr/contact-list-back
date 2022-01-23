<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace App\Repositories;

use App\Interfaces\ContactRepositoryInterface;
use App\Models\Contact;
use Exception;
use Illuminate\Validation\ValidationException;

class ContactRepository implements ContactRepositoryInterface
{
    /**
     * @return Contact[]
     */
    public function getAllContacts(): array
    {
        return Contact::get()->toArray();
    }

    /**
     * @param $contactId
     * @return mixed
     */
    public function getContactById($contactId): Contact
    {
        return Contact::findOrFail($contactId);
    }

    /**
     * @param Contact $contact
     */
    public function deleteContact(Contact $contact): void
    {
        $contact->delete();
    }

    /**
     * @param Contact $contact
     * @param array $data
     * @return mixed
     * @throws ValidationException
     * @throws Exception
     */
    public function saveContact(Contact $contact, array $data): Contact
    {
        $contact->fill($data);
        $contact->validate();
        $contact->save();
        return $contact;
    }
}
