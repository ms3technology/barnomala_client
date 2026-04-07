<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SSOController extends Controller
{
    /**
     * Handle the tenant SSO login request from the Cloud Platform.
     * Validates HMAC signature using CLIENT_SSO_KEY and logs in a single admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $payload = $request->query('payload');
        $signature = $request->query('signature');

        if (!$payload || !$signature) {
            return response()->view('errors.sso-missing', [
                'title' => 'Invalid SSO Request',
                'message' => 'The SSO payload or signature is missing. Please start the login process again from Barnomala Platform Dashboard or School Settings.',
            ], 400);
        }

        // 1. Verify Signature
        $secret = env('CLIENT_SSO_KEY');
        
        if (!$secret) {
            Log::error('SSO Error: CLIENT_SSO_KEY is not configured.');
            return response()->view('errors.sso-missing', [
                'title' => 'SSO Configuration Missing',
                'message' => 'SSO is temporarily unavailable because the server key is not configured. Please contact support.',
            ], 500);
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('SSO Error: Invalid signature detected.');
            abort(403, 'Invalid signature');
        }

        // 2. Decode Payload
        $data = json_decode(base64_decode($payload), true);

        if (!$data || !isset($data['expires_at'])) {
            abort(400, 'Malformed payload');
        }

        // 3. Validate Expiration
        if ($data['expires_at'] < now()->timestamp) {
            abort(403, 'SSO Token Expired');
        }

        // 4. Always login the single admin account
        $adminEmail = env('CLIENT_ADMIN_EMAIL', 'admin@barnomala.com');
        $user = User::where('email', $adminEmail)->first();

        if (!$user) {
            Log::error("SSO Error: Admin user with email {$adminEmail} not found in database.");
            abort(404, 'Admin user not found');
        }

        // 5. Establish Session
        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/admin');
    }
}
