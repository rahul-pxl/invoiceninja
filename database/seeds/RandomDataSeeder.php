<?php

use App\DataMapper\ClientSettings;
use App\DataMapper\CompanySettings;
use App\DataMapper\DefaultSettings;
use App\Models\Account;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\CompanyToken;
use App\Models\GroupSetting;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RandomDataSeeder extends Seeder
{
    use \App\Utils\Traits\MakesHash;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->command->info('Running RandomDataSeeder');

        Eloquent::unguard();

        $faker = Faker\Factory::create();

        $account = factory(\App\Models\Account::class)->create();
        $company = factory(\App\Models\Company::class)->create([
            'account_id' => $account->id,
            'domain' => 'ninja.test',
        ]);

        $account->default_company_id = $company->id;
        $account->save();

        $user = factory(\App\Models\User::class)->create([
            'email'             => $faker->email,
           // 'account_id' => $account->id,
            'confirmation_code' => $this->createDbHash(config('database.default'))
        ]);

        $company_token = CompanyToken::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'account_id' => $account->id,
            'name' => 'test token',
            'token' => str_random(64),
        ]);

        $user->companies()->attach($company->id, [
            'account_id' => $account->id,
            'is_owner' => 1,
            'is_admin' => 1,
            'is_locked' => 0,
            'permissions' => json_encode([]),
            'settings' => json_encode(DefaultSettings::userSettings()),
        ]);

        $client = factory(\App\Models\Client::class)->create([
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);


        ClientContact::create([
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => config('ninja.testvars.username'),
            'company_id' => $company->id,
            'password' => Hash::make(config('ninja.testvars.password')),
            'email_verified_at' => now(),
            'client_id' =>$client->id,
        ]);


        factory(\App\Models\Client::class, 6)->create(['user_id' => $user->id, 'company_id' => $company->id])->each(function ($c) use ($user, $company){

            factory(\App\Models\ClientContact::class,1)->create([
                'user_id' => $user->id,
                'client_id' => $c->id,
                'company_id' => $company->id,
                'is_primary' => 1
            ]);

            factory(\App\Models\ClientContact::class,10)->create([
                'user_id' => $user->id,
                'client_id' => $c->id,
                'company_id' => $company->id
            ]);

        });

        /** Product Factory */
        factory(\App\Models\Product::class,50)->create(['user_id' => $user->id, 'company_id' => $company->id]);

        /** Invoice Factory */
        factory(\App\Models\Invoice::class,500)->create(['user_id' => $user->id, 'company_id' => $company->id, 'client_id' => $client->id, 'settings' => ClientSettings::buildClientSettings($company->settings, $client->settings)]);

        /** Recurring Invoice Factory */
        factory(\App\Models\RecurringInvoice::class,20)->create(['user_id' => $user->id, 'company_id' => $company->id, 'client_id' => $client->id]);

       // factory(\App\Models\Payment::class,20)->create(['user_id' => $user->id, 'company_id' => $company->id, 'client_id' => $client->id, 'settings' => ClientSettings::buildClientSettings($company->settings, $client->settings)]);

        factory(\App\Models\Payment::class,20)->create(['user_id' => $user->id, 'company_id' => $company->id, 'client_id' => $client->id]);


        $clients = Client::all();

        foreach($clients as $client)
        {
            //$client->getNextClientNumber($client);
            $client->id_number = $client->getNextClientNumber($client);
            $client->save();
        }


        GroupSetting::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'settings' =>  ClientSettings::buildClientSettings(new CompanySettings(CompanySettings::defaults()), new ClientSettings(ClientSettings::defaults())),
            'name' => 'Default Client Settings',
        ]);
        
    }
    
}