<?php

namespace App\Controllers;

use App\Libraries\HansardPdfParser;
use App\Libraries\OpenAI; // Assuming you have this library
use CodeIgniter\Controller;

class HansardSummary extends Controller
{
    public function generateSummary()
    {
        // ... (previous code) ...

            // 7. Process each chunk (summarize, store, etc.)
            $summary = ""; // Initialize an empty string to store the complete summary
            foreach ($chunks as $chunk) {
                // a. Call the OpenAI library with system and user messages
                $openai = new OpenAI(); // Initialize your OpenAI library

                $response = $openai->generateSummary([
                    'system' => 'You are a highly accurate and knowledgeable political assistant with extensive experience as a journalist. Using the provided text from Hansard, generate a concise and engaging summary. Highlight key meetings and debates, noting significant or noteworthy speakers. Include quotes that cite the MP’s name, their party affiliation, constituency, and any relevant background information. Ensure your response is accessible and interesting for a general audience. Additionally, as this text may be part of a larger document, craft your summary to flow naturally within a broader context while still being effective as a standalone reply.',
                    'user' => $chunk
                ]);

                // b. Append the summarized chunk to the overall summary
                $summary .= $response . "\n\n"; // Add some spacing between chunks
            }

            // 8. Send the complete summary via email
            $this->sendEmail($summary);

        } catch (\Exception $e) {
            // ... (error handling) ...
        }
    }

    // ... (other methods) ...

    /**
     * Sends the summary via email.
     *
     * @param string $summary The complete Hansard summary.
     * @return void
     */
    private function sendEmail(string $summary): void
    {
        // 1. Fetch subscriber emails from your database (replace with your logic)
        $subscribers = $this->getSubscriberEmails();

        // 2. Initialize PHPMailer (replace with your email sending logic)
        $email = \Config\Services::email();

        // 3. Set email parameters and send to each subscriber
        $email->setFrom('your_email@example.com', 'Hansard Summary');
        $email->setSubject('Your Daily Hansard Summary');
        $email->setMessage($summary);

        foreach ($subscribers as $subscriber) {
            $email->setTo($subscriber);
            $email->send();
        }
    }

    /**
     * Fetches subscriber emails from the database.
     *
     * @return array An array of subscriber email addresses.
     */
    private function getSubscriberEmails(): array
    {
        // Replace this with your database query logic
        // Example using a model:
        // $subscriberModel = new SubscriberModel();
        // $subscribers = $subscriberModel->findAll();
        // $emails = array_column($subscribers, 'email');
        // return $emails;

        // Placeholder for demonstration
        return ['heather.herbert.1975@gmail.com'];
    }
}
