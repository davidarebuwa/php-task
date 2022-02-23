<?php

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return home page
    //return view('welcome');


    $client = new Client();
    $page = 1; //we are starting from the first page
    $call = $client->get('https://reqres.in/api/users?page=' . $page);
    $response = json_decode($call->getBody()->getContents(), true);
    $records = $response['data'];

    $num_pages = $response['total_pages'];
    $current_page = $response['page'];
    if ($current_page < $num_pages) {
        $next_page = $current_page + 1;
    } else {
        $next_page = $current_page;
    }
    //iterate through the pages
    if ($num_pages > 1) {
        for ($i = $next_page; $i <= $num_pages; $i++) {
            $call = $client->get('https://reqres.in/api/users?page=' . $i);
            $response = json_decode($call->getBody()->getContents(), true);
            $data = $response['data'];
            foreach ($data as $datum) {
                $records[] = $datum;
            }
        }
    } else {
        //return default
        $records = $response['data'];
    }

    $users = []; //array to hold data from all pages
    foreach ($records as $record) {

        $u = new User;
        $u->name = $record['first_name'] . ' ' . $record['last_name'];
        $u->email = $record['email'];
        $u->password = Hash::make(Str::random(12));
        $users[] = $u;
    }

    \Log::info($users);
});
