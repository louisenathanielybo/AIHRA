<?php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

class DialogflowService
{
    protected $sessionsClient;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = 'aihra-472311';
        $this->sessionsClient = new SessionsClient([
            'credentials' => base_path('aihra-key.json'),
        ]);
    }

    public function detectIntent($queryText, $sessionId)
    {
        $session = $this->sessionsClient->sessionName($this->projectId, $sessionId);

        $textInput = new TextInput();
        $textInput->setText($queryText);
        $textInput->setLanguageCode('en');

        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        $response = $this->sessionsClient->detectIntent($session, $queryInput);

        return $response->getQueryResult();
    }

    public function close()
    {
        $this->sessionsClient->close();
    }
}
