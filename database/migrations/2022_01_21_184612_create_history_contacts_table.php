<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

use App\Models\History;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHistoryContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->string(History::FIRST_NAME_FIELD);
            $table->string(History::LAST_NAME_FIELD);
            $table->string(History::EMAIL_FIELD);
            $table->string(History::PHONE_FIELD);
            $table->timestamp(History::CREATED_AT)->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreignId(History::CONTACT_ID)->constrained('contacts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
