<?php

namespace App\Console\Commands;

use App\Services\BookingRuleEngine;
use Illuminate\Console\Command;

class DispatchPreExpiryReminders extends Command
{
    protected $signature = 'antrekita:dispatch-reminders';

    protected $description = 'Detect bookings within 5 minutes of expiry and dispatch dual WA/Email alerts.';

    public function handle(BookingRuleEngine $engine): int
    {
        $dispatched = $engine->processPreExpiryAlerts();

        $this->info('Dispatched ' . count($dispatched) . ' H-5 pre-expiry reminder(s).');

        return self::SUCCESS;
    }
}
