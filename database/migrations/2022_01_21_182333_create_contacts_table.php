<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

use App\Models\Contact;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(app(Contact::class)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->char(Contact::FIRST_NAME_FIELD, 30);
            $table->char(Contact::LAST_NAME_FIELD, 30);
            $table->char(Contact::EMAIL_FIELD, 50)->unique();
            $table->char(Contact::PHONE_FIELD, 16);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(app(Contact::class)->getTable());
    }
}
