<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace App\Interfaces;

use App\Models\Contact;

interface ContactRepositoryInterface
{
    /**
     * @return array
     */
    public function getAllContacts(): array;

    /**
     * @param int $contactId
     * @return Contact
     */
    public function getContactById(int $contactId): Contact;

    /**
     * @param Contact $contact
     */
    public function deleteContact(Contact $contact): void;

    /**
     * @param Contact $contact
     * @param array $data
     * @return Contact
     */
    public function saveContact(Contact $contact, array $data): Contact;
}
