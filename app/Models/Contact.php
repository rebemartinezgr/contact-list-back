<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    const ENTITY_ID_FIELD = 'id';
    const FIRST_NAME_FIELD = 'first_name';
    const LAST_NAME_FIELD = 'last_name';
    const EMAIL_FIELD = 'email';
    const PHONE_FIELD = 'phone';
    const DELETED_AT = 'deleted_at';
    const HISTORY_RELATION = 'history';

    protected $fillable = [
        self::FIRST_NAME_FIELD,
        self::LAST_NAME_FIELD,
        self::EMAIL_FIELD,
        self::PHONE_FIELD
    ];

    /**
     * @var ContactValidator
     */
    private ContactValidator $contactValidator;

    /**
     * @param array $attributes
     */
    public function __construct(
        array $attributes = [],
        ?ContactValidator $contactValidator = null
    ) {
        $this->contactValidator = $contactValidator ?? resolve(ContactValidator::class);
        parent::__construct($attributes);
    }

    /**
     * @throws ValidationException
     */
    public function validate(): void
    {
        $this->contactValidator->getValidator($this)->validate();
    }

    /**
     * @return HasMany
     */
    public function history(): HasMany
    {
        return $this->hasMany(History::class);
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return array_reverse($this->history()->get()->toArray());
    }

    /**
     * @overide in order to save history with original data before saving
     *
     * @param array $options
     * @return bool
     * @throws \Exception
     */
    public function save(array $options = []): bool
    {
        if (!$this->isDirty()) {
            return true;
        }
        /* contact and history is saved consistently in same transaction */
        DB::beginTransaction();
        try {
            if ($this->exists) {
                $history = $this->createHistory();
            }
            $saved = parent::save($options);
            if ($saved && isset($history)) {
                $this->history()->save($history);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception(__('Unable to save the contact.') . $e->getMessage());
        }
    }

    /**
     * @return History
     */
    private function createHistory(): History
    {
        $history = new History;
        /* Copy original values from contact to history */
        $data = Arr::only($this->getOriginal(), $this->fillable);
        $history->fill($data);

        return $history;
    }
}
