<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function handleChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $userMessage = strtolower(trim($request->input('message')));

        // Step-by-step responses for specific questions
        $customResponses = [
            'top up' => [
                'question' => 'How do I top up my balance?',
                'steps' => [
                    'Go to the "Top Up Balance" section from your user dashboard.',
                    'Click "Upload Receipt".',
                    'Choose your payment screenshot (must be JPG, PNG, under 2MB).',
                    'Click "Submit" to send the receipt for admin approval.',
                    'Wait for an admin to approve your top-up. You’ll be notified once it’s accepted.'
                ],
                'info' => 'Make sure the screenshot is clear and the amount is visible. Only image formats under 2MB are accepted.',
                'conclusion' => 'Once approved, your balance will be updated automatically.'
            ],
            'parking cost' => [
                'question' => 'How much is parking?',
                'steps' => [
                    'The parking fee is 30 birr per hour.',
                    'The system calculates your parking cost based on how long your session lasts.',
                    'Example: Parking for 2 hours = 60 birr.'
                ],
                'info' => 'Payment is deducted from your balance when you end a session.',
                'conclusion' => 'Always make sure your balance is sufficient to end parking.'
            ],
            'update password' => [
                'question' => 'How can I update my password?',
                'steps' => [
                    'Login and go to your Profile page.',
                    'Click the "Edit Profile" or "Change Password" button.',
                    'Enter your current password, then your new password.',
                    'Confirm the new password and click "Save Changes".',
                    'You will see a confirmation message once it’s updated.'
                ],
                'info' => 'Use a strong password (at least 8 characters, with letters and numbers).',
                'conclusion' => 'You can now log in using your new password.'
            ]
        ];

        // Check for match
        foreach ($customResponses as $keyword => $data) {
            if (str_contains($userMessage, $keyword)) {
                $stepsFormatted = '';
                foreach ($data['steps'] as $index => $step) {
                    $stepsFormatted .= "Step " . ($index + 1) . ": " . $step . "\n";
                }

                return response()->json([
                    'user' => $user ? $user->name : 'Guest',
                    'message' => $request->input('message'),
                    'bot' => <<<REPLY
📌 Question: {$data['question']}
📝 Answer:
$stepsFormatted
📊 Supporting Info: {$data['info']}
✅ Conclusion: {$data['conclusion']}
REPLY
                ]);
            }
        }

        // Fallback to Gemini API
        $systemContext = <<<EOT
You are a helpful assistant for "Miki's Smart Parking System", a web application for parking management.

✅ Only answer questions related to this system, such as:
- Registering, logging in, updating email/password
- Topping up balance by uploading receipts
- Starting, reserving, and ending parking sessions
- Admin approval of balance top-ups
- Viewing parking history

🚫 If the question is unrelated to these (e.g., weather, cooking, news), respond:
"Sorry, I can only help with questions about Miki's Smart Parking System."

📐 Always reply in this structured format:

📌 Question: [Repeat user's question]
📝 Answer: [Brief, direct explanation with steps if needed]
📊 Supporting Info: [Details like file types, limits, permissions, etc.]
✅ Conclusion: [Summary or result]

User: {$userMessage}
Assistant:
EOT;

        try {
            $response = Http::withOptions([
                'verify' => false
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemContext]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to get a response from the Gemini API'], 500);
            }

            $data = $response->json();
            $botReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from Gemini.';

            return response()->json([
                'user' => $user ? $user->name : 'Guest',
                'message' => $userMessage,
                'bot' => $botReply
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Chat error: ' . $e->getMessage()], 500);
        }
    }
}
