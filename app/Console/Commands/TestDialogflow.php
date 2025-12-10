<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestDialogflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dialogflow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Dialogflow SSL fix...');
        
        try {
            $dialogflow = new \App\Services\DialogflowService();
            $result = $dialogflow->detectIntent('What are the working hours?', 'test-session-' . time());
            
            $this->info('✅ Success!');
            $this->info('Response: ' . $result->getFulfillmentText());
            $this->info('Confidence: ' . $result->getIntentDetectionConfidence());
            
            $dialogflow->close();
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed: ' . $e->getMessage());
            return 1;
        }
    }
}
