<?php
declare(strict_types=1);
/*
 * @author Rebeca Martínez García <r.martinezgr@gmail.com>
 */

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\History;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    const REGISTER_COUNT = 10;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $initialId = Contact::max('id') + 1;
        /* Create nine dummy contact registers with one history */
        for($i = $initialId; $i <= $initialId+self::REGISTER_COUNT; $i++){
            $id = DB::table(app(Contact::class)->getTable())->insertGetId(array(
                Contact::FIRST_NAME_FIELD => 'First name '.$i,
                Contact::LAST_NAME_FIELD => 'Last name '.$i,
                Contact::EMAIL_FIELD => "email.$i@gmail.com ",
                Contact::PHONE_FIELD => '6'. str_repeat((string) $i, 4),
                Contact::CREATED_AT => date('Y-m-d H:i:s')
            ));
            DB::table('histories')->insert(array(
                History::FIRST_NAME_FIELD => 'First name old '.$i,
                History::LAST_NAME_FIELD => 'Last name old '.$i,
                History::EMAIL_FIELD => "email.old.$i@gmail.com ",
                History::PHONE_FIELD => '+34 6'. str_repeat((string) $i, 8),
                History::CREATED_AT => date('Y-m-d H:i:s'),
                History::CONTACT_ID => $id
            ));
        }

        $this->command->info('Contact dummy data has been created');
    }
}
