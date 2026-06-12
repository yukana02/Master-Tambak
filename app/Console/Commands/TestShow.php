<?php

namespace App\Console\Commands;

use App\Models\Pond;
use App\Models\PondHarvestInput;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

class TestShow extends Command
{
    protected $signature = 'test:show';

    public function handle()
    {
        $user = User::first();
        auth()->login($user);

        $pond = Pond::first();
        $input = PondHarvestInput::create([
            'pond_id' => $pond->id,
            'harvested_at' => now(),
            'bucket_name' => 'Test Bakul',
            'kg' => 10,
            'price_per_kg' => 15000,
            'total_price' => 150000,
            'status' => 'draft',
        ]);

        $request = Request::create('/ponds/'.$pond->id, 'GET');
        $response = app(Kernel::class)->handle($request);

        if ($response->getStatusCode() !== 200) {
            $this->error('Status: '.$response->getStatusCode());
            $this->error(strip_tags(substr($response->getContent(), 0, 1000)));
        } else {
            $this->info('OK rendered '.strlen($response->getContent()).' bytes');
        }

        $input->delete();
    }
}
