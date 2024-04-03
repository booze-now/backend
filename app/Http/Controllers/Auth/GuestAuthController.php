<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;


class GuestAuthController extends Controller
{
    protected $guard = 'guard_guest';

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        Auth::shouldUse($this->guard);
        $credentials = request(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => __('Unauthorized?')], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout(true); # This is just logout function that will destroy access token of current user
        return response()->json(['message' => __('Successfully logged out')]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        # When access token will be expired, we are going to generate a new one wit this function
        # and return it here in response
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        # This function is used to make JSON response with new
        # access token of current user

        return response()->json([
            'user' => auth()->user(),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function forgotPassword(Request $request) //: JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.

        $guest = Guest::where(['email', $request->email])->get();

        if ($guest === null) {
            return response()->json(['error' => __('Invalid data #3')], 401);
        }

        ResetPassword::$createUrlCallback = function ($notifiable) {
            $data = json_decode($notifiable->data, JSON_OBJECT_AS_ARRAY);
            $guid = Str::uuid()->toString();
            $data['reset_password_guid'] = $guid;
            $notifiable->data = $data;
            $notifiable->save();

            // Log::info('notif: ' . var_export($notifiable, true));
            return implode('/', [Config::get('app.frontend_url'), 'reset-password', $notifiable->id, $guid]);
        };

        // $this->notify(new ResetPasswordNotification($url));
        // $user->sendPasswordResetNotification('');
        // return static::RESET_LINK_SENT;

        $status = Password::broker('guests')->sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)]);
    }

    /**
     * Handle an incoming reset password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function resetPassword(Request $request) // : JsonResponse
    {
        $request->validate([
            'id' => ['required'],
            'guid' => ['uuid', 'required'],
            'password' => ['required',  Rules\Password::defaults()],
        ]);

        $guest = Guest::findOrFail($request->id);
        $data = json_decode($guest->data);
        if (empty($data['reset_password_guid'])) {
            return response()->json(['error' => __('Invalid data #1')], 401);
        }

        if ($data['reset_password_guid'] !== $request->guid) {
            return response()->json([
                'error' => __('Invalid data #2'),
                'stored_guid' => $data['reset_password_guid'],
                'sent_guid' => $request->guid,
            ], 401);
        }

        unset($data['reset_password_guid']);

        $guest->forceFill([
            'password' => Hash::make($request->password),
            'data' => $data,
        ])->save();

        event(new PasswordReset($guest));

        return response()->json(['message' => __('Your password has been updated')]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request): Response
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . Guest::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $guest = Guest::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        VerifyEmail::$createUrlCallback = function ($notifiable) {
            $data = $notifiable->data;
            $guid = Str::uuid()->toString();
            $data['verification_guid'] = $guid;
            $notifiable->data = $data;
            $notifiable->save();

            return implode('/', [Config::get('app.frontend_url'), 'confirm-registration', $notifiable->id, $guid]);
        };

        event(new Registered($guest));

        Auth::login($guest);

        return response()->noContent();
    }
    public function confirmRegistration(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'guid' => ['uuid', 'required'],
        ]);

        $guest = Guest::findOrFail($request->id);
        $data = $guest->data;

        if (empty($data['verification_guid'])) {
            return response()->json(['error' => __('Invalid data #1')], 401);
        }
        if (!empty($guest->hasVerifiedEmail())) {
            return response()->json(['message' => __('Your email is already verified')], 200);
        }

        if ($data['verification_guid'] !== $request->guid) {
            return response()->json([
                'error' => __('Invalid data #2'),
                'stored_guid' => $data['verification_guid'],
                'sent_guid' => $request->guid,
            ], 401);
        }

        unset($data['verification_guid']);

        $guest->forceFill([
            'password' => Hash::make($request->password),
            'data' => $data,
            'email_verified_at' => \Date::now(),
        ])->save();

        event(new PasswordReset($guest));

        return response()->json(['message' => __('Your account has been activated')]);

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
    }

    public function resendEmailVerificationMail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ]);
        $guest = Guest::where('email', $request->email)
            ->whereNull('email_verified_at')->first();
        if ($guest) {
            VerifyEmail::$createUrlCallback = function ($notifiable) {
                $data = $notifiable->data;
                $guid = Str::uuid()->toString();
                $data['verification_guid'] = $guid;
                $notifiable->data = $data;
                $notifiable->save();

                return implode('/', [Config::get('app.frontend_url'), 'confirm-registration', $notifiable->id, $guid]);
            };
            $guest->sendEmailVerificationNotification();
        }
        return ['message' => __('We have received your request. ' .
            'If there is an unconfirmed registration associated with ' .
            'the provided email address, we have sent a confirmation email. ' .
            'Please check your inbox, including the spam or promotions folder.')];
    }

    public function reset()
    {
        # When access token will be expired, we are going to generate a new one with this function
        # and return it here in response
        return request()->all();
    }
}
