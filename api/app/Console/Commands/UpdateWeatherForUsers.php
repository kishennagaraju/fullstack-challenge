<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Weather as WeatherModel;
use App\Service\Weather as WeatherService;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateWeatherForUsers extends Command
{
    /**
     * @var WeatherService
     */
    private $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;

        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is created to update the weather data for users on hourly basis.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            User::query()
                ->with(['weather'])
                ->chunk(50, function ($users) {
                    foreach ($users as $user) {
                        $weatherResponse = $this->weatherService->getWeatherByLatLng(
                            $user->latitude,
                            $user->longitude
                        );

                        if ($weatherResponse->status() === Response::HTTP_OK) {
                            $weatherData = json_decode($weatherResponse->body(), true);

                            if (!$user->weather) {
                                $weather = new WeatherModel();
                                $weather->hourly = $weatherData['hourly'];
                                $weather->current = $weatherData['current'];
                                $weather->daily = $weatherData['daily'];
                                $weather->latitude = $weatherData['lat'];
                                $weather->longitude = $weatherData['lon'];
                                $weather->timezone = $weatherData['timezone'];
                                $user->weather()->save($weather);
                            } else {
                                $user->weather()->update([
                                    'hourly' => $weatherData['hourly'],
                                    'current' => $weatherData['current'],
                                    'daily' => $weatherData['daily'],
                                    'latitude' => $weatherData['lat'],
                                    'longitude' => $weatherData['lon'],
                                    'timezone' => $weatherData['timezone'],
                                ]);
                            }
                        }
                    }
                });

            DB::commit();
        } catch (\Throwable $ex) {
            report($ex);
            $this->error($ex->getMessage());
            DB::rollBack();
        }

        $this->info("Done !!!");
    }
}
