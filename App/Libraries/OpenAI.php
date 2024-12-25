<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class OpenAI
{
    /**
     * @var string The OpenAI API key.
     */
    private $apiKey;

    /**
     * @var string The base URL for the OpenAI API.
     */
    private $apiUrl = 'https://api.openai.com/v1/chat/completions';

    /**
     * OpenAI constructor.
     */
    public function __construct()
    {
        $this->apiKey = getenv('OPENAI_API_KEY'); // Get API key from environment variable
    }

    /**
     * Sends a request to the OpenAI API to generate a summary.
     *
     * @param array $messages An array containing 'system' and 'user' messages.
     * @return string The generated summary from the API response.
     * @throws \Exception If there's an error with the API request or response.
     */
    public function generateSummary(array $messages): string
    {
        // 1. Prepare the request data
        $requestData = [
            'model' => 'gpt-4o-mini-2024-07-18', // You can change the model if needed
            'messages' => [
                ['role' => 'system', 'content' => $messages['system']],
                ['role' => 'user', 'content' => $messages['user']],
            ],
        ];

        // 2. Create a CURLRequest instance
        $client = service('curlrequest', [
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);

        // 3. Send the API request
        $response = $client->post('', [
            'json' => $requestData,
        ]);

        // 4. Check for API errors
        if (!$response->isOk()) {
            $errorMessage = 'OpenAI API request failed: ' . $response->getStatusCode() . ' - ' . $response->getReason();
            log_message('error', $errorMessage);
            throw new \Exception($errorMessage);
        }

        // 5. Decode the JSON response
        $responseData = json_decode($response->getBody(), true);

        // 6. Check if the response has the expected format
        if (isset($responseData['choices'][0]['message']['content'])) {
            return $responseData['choices'][0]['message']['content'];
        } else {
            $errorMessage = 'Unexpected OpenAI API response format: ' . json_encode($responseData);
            log_message('error', $errorMessage);
            throw new \Exception($errorMessage);
        }
    }
}