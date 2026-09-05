<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client as TwilioClient;

class SmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public $phoneNumber,
        public string $body
    ) {}

    public function handle(): void
    {
        \Log::info("Sending SMS to {$this->phoneNumber}: \"{$this->body}\"");
        if (empty($this->phoneNumber) || empty($this->body) || $this->phoneNumber === '+10000000000') {
            return;
        }

        $twilio = new TwilioClient(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );

        try {
            $twilio->messages->create(
                $this->phoneNumber,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $this->body
                ]
            );
        } catch (\Exception $e) {
            \Log::error("Failed to send SMS to {$this->phoneNumber}: " . $e->getMessage());
        }
    }
}