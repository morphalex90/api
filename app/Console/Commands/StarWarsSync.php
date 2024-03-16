<?php

namespace App\Console\Commands;

use App\Models\SW\Person;
use App\Models\SW\Planet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class StarWarsSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'starwars:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save all data from https://swapi.dev';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->people();
        $this->planets();
    }

    private function people($page = 1)
    {
        //############################## People
        $response = Http::get('https://swapi.dev/api/people/?page=' . $page);
        if ($response->status() == 200) {
            $body = json_decode($response->body());
            $this->info(print_r($body, 1));

            foreach ($body->results as $person) {
                $planet = explode('/', $person->homeworld);

                Person::updateOrCreate(
                    ['name' => $person->name],
                    [
                        'name' => $person->name,
                        'height' => ($person->height != 'unknown' && $person->height != 'n/a' ? $person->height : null),
                        'mass' => ($person->mass != 'unknown' && $person->mass != 'n/a' ? str_replace(',', '', $person->mass) : null),
                        'hair_color' => ($person->hair_color != 'unknown' && $person->hair_color != 'n/a' ? $person->hair_color : null),
                        'skin_color' => ($person->skin_color != 'unknown' && $person->skin_color != 'n/a' ? $person->skin_color : null),
                        'eye_color' => ($person->eye_color != 'unknown' && $person->eye_color != 'n/a' ? $person->eye_color : null),
                        'birth_year' => ($person->birth_year != 'unknown' && $person->birth_year != 'n/a' ? $person->birth_year : null),
                        'gender' => ($person->gender != 'unknown' && $person->gender != 'n/a' ? $person->gender : null),
                        'planet_id' => $planet[5],
                    ]
                );
            }

            if ($body->next != '') {
                $this->people(substr($body->next, -1));
            }
        }
    }

    private function planets($page = 1)
    {
        //############################## Planets
        $response = Http::get('https://swapi.dev/api/planets/?page=' . $page);
        if ($response->status() == 200) {
            $body = json_decode($response->body());
            $this->info(print_r($body, 1));

            foreach ($body->results as $person) {

                Planet::updateOrCreate(
                    ['name' => $person->name],
                    [
                        'name' => $person->name,
                        'rotation_period' => ($person->rotation_period != 'unknown' && $person->rotation_period != 'n/a' ? $person->rotation_period : null),
                        'orbital_period' => ($person->orbital_period != 'unknown' && $person->orbital_period != 'n/a' ? $person->orbital_period : null),
                        'diameter' => ($person->diameter != 'unknown' && $person->diameter != 'n/a' ? $person->diameter : null),
                        'climate' => ($person->climate != 'unknown' && $person->climate != 'n/a' ? $person->climate : null),
                        'gravity' => ($person->gravity != 'unknown' && $person->gravity != 'n/a' ? $person->gravity : null),
                        'terrain' => ($person->terrain != 'unknown' && $person->terrain != 'n/a' ? $person->terrain : null),
                        'surface_water' => ($person->surface_water != 'unknown' && $person->surface_water != 'n/a' ? $person->surface_water : null),
                        'population' => ($person->population != 'unknown' && $person->population != 'n/a' ? $person->population : null),
                    ]
                );
            }

            if ($body->next != '') {
                $this->planets(substr($body->next, -1));
            }
        }
    }
}
