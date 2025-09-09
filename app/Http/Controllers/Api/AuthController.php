<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication endpoints"
 * )
 */

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="My Social Media API",
 *      description="API documentation for Social Media app",
 *      @OA\Contact(email="support@example.com")
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Local API Server"
 * )
 */



class AuthController extends Controller
{
    private AuthService $authService;

    // Inject AuthService instead of handling logic directly
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Register a user with name, email, password and confirmPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","confirmPassword"},
     *             @OA\Property(property="name", type="string", example="Abdul Hanan"),
     *             @OA\Property(property="email", type="string", example="hanan@example.com"),
     *             @OA\Property(property="password", type="string", example="secret123"),
     *             @OA\Property(property="confirmPassword", type="string", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registered Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registered Successfully"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(Request $request)
    {
        // ✅ Validate input before passing to service
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'confirmPassword' => 'required|same:password',
        ]);

        // ✅ Delegate business logic to service
        $response = $this->authService->register($validated);

        // ✅ Return service response
        return response()->json($response, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Login user",
     *     description="Login with email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="hanan@example.com"),
     *             @OA\Property(property="password", type="string", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login Successful"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // ✅ Delegate to service
        $response = $this->authService->login($validated);

        // If service returns null → invalid credentials
        if (!$response) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json($response, 200);
    }
}
