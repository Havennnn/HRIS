<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Traits\BuildsApiResponses;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use BuildsApiResponses;

    /**
     * Login employee and issue an API access token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $employee = Employee::query()->where('email', $validated['email'])->first();

        if (! $employee || ! Hash::check($validated['password'], $employee->password)) {
            return $this->errorResponse('Invalid credentials.', Response::HTTP_UNAUTHORIZED);
        }

        $token = $employee->createToken('employee-api')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'employee' => [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
            ],
        ], 'Login successful.');
    }

    /**
     * Logout employee and revoke current API access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $employee = $request->user();

        if (! $employee instanceof Employee) {
            return $this->unauthorizedResponse('Unauthorized.');
        }

        $employee->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Logout successful.');
    }

    /**
     * Send a reset-password link to an employee email.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $employee = Employee::query()->where('email', $validated['email'])->first();

        if ($employee) {
            $employee->sendPasswordResetLink();
        }

        return $this->successResponse(
            data: null,
            message: 'If the email exists, a password reset link has been sent.'
        );
    }

    /**
     * Validate whether a reset token is still valid.
     */
    public function verifyResetToken(string $token): JsonResponse
    {
        $employee = Employee::query()
            ->where('password_reset_token', hash('sha256', $token))
            ->where('password_reset_expires_at', '>', now())
            ->first();

        if (! $employee) {
            return $this->errorResponse('This password reset link has expired or is invalid.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->successResponse([
            'token' => $token,
            'email' => $employee->email,
        ], 'Password reset token is valid.');
    }

    /**
     * Update employee password by reset token.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $employee = Employee::query()
            ->where('password_reset_token', hash('sha256', $validated['token']))
            ->where('password_reset_expires_at', '>', now())
            ->first();

        if (! $employee || ! $employee->verifyPasswordResetToken($validated['token'])) {
            return $this->errorResponse('This password reset token is invalid.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $employee->forceFill([
            'password' => $validated['password'],
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ])->save();

        return $this->successResponse(null, 'Password has been reset successfully.');
    }
}
