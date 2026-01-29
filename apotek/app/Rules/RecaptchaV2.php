<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaV2 implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!config('recaptcha.secret_key')) {
            Log::warning('reCAPTCHA secret key not configured');
            // In production, don't fail - instead skip validation
            // In development, you might want to be strict
            if (app()->isProduction()) {
                return; // Skip validation if not configured
            }
        }

        if (empty($value)) {
            $fail('Please complete the reCAPTCHA verification.');
            return;
        }

        try {
            $response = Http::asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => config('recaptcha.secret_key'),
                    'response' => $value,
                ]
            );

            $data = $response->json();

            // Check if verification was successful
            if (!isset($data['success']) || !$data['success']) {
                Log::warning('reCAPTCHA verification failed', [
                    'error_codes' => $data['error-codes'] ?? [],
                    'score' => $data['score'] ?? null,
                ]);
                $fail('reCAPTCHA verification failed. Please try again.');
                return;
            }

            // Optional: Check score for reCAPTCHA v3 (if upgraded)
            // if (isset($data['score']) && $data['score'] < 0.5) {
            //     $fail('reCAPTCHA score too low. Please try again.');
            // }

            Log::debug('reCAPTCHA verification successful', [
                'score' => $data['score'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('reCAPTCHA API error', [
                'error' => $e->getMessage(),
            ]);
            $fail('Unable to verify reCAPTCHA. Please try again.');
        }
    }
}
