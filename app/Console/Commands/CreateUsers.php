<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class CreateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls data from api and populates into user model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://reqres.in/api/users?page=1');
        $data = json_decode($response->getBody()->getContents());

        //for each user in the response, create a new user model and save it to the database
        foreach ($data->data as $user) {
            $u = new User;
            $u->name = $user->first_name;
            $u->email = $user->email;
            $u->password = Hash::make(Str::random(12));
            \Log::info($u);
            //$u->save();
        }
    }
}
