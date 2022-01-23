<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace App\Http\Controllers;

use App\Models\Contact;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Interfaces\ContactRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    const DELETE_SUCCESS_MSG = 'Contact deleted successfully.';
    const DELETE_ERROR_MSG = 'Something goes wrong while deleting a contact.';
    const UPDATE_ERROR_MSG = 'Something goes wrong while updating a contact.';
    const UPDATE_SUCCESS_MSG = 'Contact updated successfully.';
    const CREATE_ERROR_MSG = 'Something goes wrong while creating a contact.';
    const CREATE_SUCCESS_MSG = 'Contact created successfully.';
    const VALIDATION_ERROR = 'Data validation error';

    const RESPONSE_ERROR_KEY = 'errors';
    const RESPONSE_MSG_KEY = 'message';
    const RESPONSE_DATA_KEY = 'contacts';

    /**
     * @var ContactRepositoryInterface
     */
    private ContactRepositoryInterface $contactRepository;

    /**
     * @param ContactRepositoryInterface $contactRepository
     */
    public function __construct(
        ContactRepositoryInterface $contactRepository
    ) {
        $this->contactRepository = $contactRepository;
    }

    /**
     * Retrieve a listing of contact.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->buildResponse(data: $this->contactRepository->getAllContacts());
    }

    /**
     * Store a newly created contact.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $contact = $this->contactRepository->saveContact(new Contact, $request->post());
            $data = $contact->toArray();
            $data[Contact::HISTORY_RELATION] = [];
            return $this->buildResponse(
                msg: __(self::CREATE_SUCCESS_MSG),
                data: $data,
                statusCode: Response::HTTP_CREATED
            );
        } catch (ValidationException $e) {

            return $this->buildResponse(
                msg: __(self::VALIDATION_ERROR),
                errors: $e->validator->errors()->all(),
                statusCode: $e->status
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->buildResponse(
                msg: __(self::CREATE_ERROR_MSG),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display the specified contact.
     *
     * @param Contact $contact
     * @return JsonResponse
     */
    public function show(Contact $contact): JsonResponse
    {
        $data = $contact->toArray();
        $data[Contact::HISTORY_RELATION] = $contact->getHistory();

        return $this->buildResponse(data: $data);
    }

    /**
     * Update the specified contact.
     *
     * @param Request $request
     * @param Contact $contact
     * @return JsonResponse
     */
    public function update(Request $request, Contact $contact): JsonResponse
    {
        try {
            $contact = $this->contactRepository->saveContact($contact, $request->post());
            $data = $contact->toArray();
            $data[Contact::HISTORY_RELATION] = $contact->getHistory();

            return $this->buildResponse(
                msg: __(self::UPDATE_SUCCESS_MSG),
                data: $data
            );
        } catch (ValidationException $e) {

            return $this->buildResponse(
                msg: __(self::VALIDATION_ERROR),
                errors: $e->validator->errors()->all(),
                statusCode: $e->status
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->buildResponse(
                msg: __(self::UPDATE_ERROR_MSG),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified contact.
     *
     * @param Contact $contact
     * @return JsonResponse
     */
    public function destroy(Contact $contact): JsonResponse
    {
        try {
            $this->contactRepository->deleteContact($contact);
            return $this->buildResponse(__(self::DELETE_SUCCESS_MSG));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->buildResponse(
                msg: __(self::DELETE_ERROR_MSG),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Build json response
     *
     * @param string $msg
     * @param array $data
     * @param array $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    private function buildResponse(string $msg = '', array $data = [], array $errors = [], int $statusCode = 200): JsonResponse
    {
        return response()->json(
            [
                self::RESPONSE_ERROR_KEY => $errors,
                self::RESPONSE_MSG_KEY => $msg,
                self::RESPONSE_DATA_KEY => $data
            ], $statusCode);
    }
}
