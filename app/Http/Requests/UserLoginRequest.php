<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Http;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => ["required", "email", "max:255", "lowercase"],
            "password" => ["required", "max:255", "min:8"],
            'recaptcha_token' => ['required', 'string'],
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $token = $this->input('recaptcha_token');

            //            if (app()->environment('local')) {
//                $result = [
//                    'success' => true,
//                    'score' => 0.2,
//                ];
//            } else {
//                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
//                    'secret' => config('services.nocaptcha.secret'),
//                    'response' => $token,
//                    'remoteip' => $this->ip(),
//                ]);
//                $result = $response->json();
//            }

            if (empty($token)) {
                $validator->errors()->add('recaptcha_token', 'The reCAPTCHA token is required.');
                return;
            }

            if (app()->isLocal() && $token === 'X-RECAPTCHA-TOKEN') {
                return;
            }
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.nocaptcha.secret'),
                'response' => $token,
                'remoteip' => $this->ip(),
            ]);

            $result = $response->json();

            if (!($result['success'] ?? false) || ($result['score'] ?? 0) < 0.5) {
                $validator->errors()->add('recaptcha_token', 'reCAPTCHA verification failed.');
            }
        });
    }



    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => [
                "message" => $validator->getMessageBag()
            ]
        ], 400));
    }
}
