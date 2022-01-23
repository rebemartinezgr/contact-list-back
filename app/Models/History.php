<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    /* Disable automatic timestamp managing */
    public $timestamps = false;

    const FIRST_NAME_FIELD = 'first_name';
    const LAST_NAME_FIELD = 'last_name';
    const EMAIL_FIELD = 'email';
    const PHONE_FIELD = 'phone';
    const CONTACT_ID = 'contact_id';


    protected $fillable = [
        self::FIRST_NAME_FIELD,
        self::LAST_NAME_FIELD,
        self::EMAIL_FIELD,
        self::PHONE_FIELD
    ];
}
