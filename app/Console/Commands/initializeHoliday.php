<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class InitializeHoliday extends Command{

    protected $signature =  'app:initialize-holiday {year}';

    protected $description = 'Fetches Philippine holidays from Calendarific API';

    public function handle()
    {
        $apiUrl = env('CALENDARIFIC_API_URL');
        $apiKey = env('CALENDARIFIC_API_KEY');
        $year = $this->argument('year');
        $response = Http::get("{$apiUrl}?api_key={$apiKey}&country=PH&year={$year}");

        $data = $response->json();

        if ($response->successful()) {
            $holidays = $data['response']['holidays'];

            foreach ($holidays as $holidayData) {
                $holiday = new Holiday();
                $holiday->name = $holidayData['name'];
                $holiday->description = $holidayData['description'];
                $holiday->date = $holidayData['date']['iso'];
                $holiday->type = $holidayData['primary_type'];
                $holiday->save();
            }
            $this->info('Philippine holidays have been fetched and saved.');
        } else {
            $error = $data['error']['message'];
            $this->error("Failed to fetch holidays. Error: {$error}");
        }
    }
}
