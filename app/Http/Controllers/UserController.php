<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckJsonIsValid;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\CheckJsonIsValid::class)
            ->only(['update','store']);
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get user collection",
     *     tags={"Users"},
     *     method="{POST}",
     *     @OA\MediaType(
     *          mediaType="application/json",
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="filter[id]",
     *          description="Filter user id",
     *          required=false,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="filter[email]",
     *          description="Filter user email",
     *          required=false,
     *          @OA\Schema(type="text")
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="filter[name]",
     *          description="Filter by name",
     *          required=false,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort",
     *          description="Sort by name, email and id. Adding '-' before the field to sort DESC. ",
     *          example="-id,name",
     *          required=false,
     *          @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="perPage",
     *          description="Pagination page size",
     *          required=false,
     *          example="5",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          description="User list page number",
     *          required=false,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/User::Collection")
     *     ),
     *     @OA\Response(response="422", description="Validation errors",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="fieldName", type="array", @OA\Items(type="string"))
     *              ),
     *          ),
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'perPage' => 'integer|min:5|max:15',
            'filter.id' => 'integer',
        ]);

        $perPage = $validated['perPage'] ?? 5;

        $users = QueryBuilder::for(User::class)
            ->defaultSort('name')
            ->allowedSorts(['name','email','id'])
            ->allowedFilters(['name','email',
                AllowedFilter::exact('id')
            ])

            ->paginate($perPage)
            ;

        return new UserCollection($users);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user info",
     *     tags={"Users"},
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="User Id",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response="200", description="Successful operation ",
     *          @OA\JsonContent(ref="#/components/schemas/User::Info")
     *      ),
     *     @OA\Response(response="404", description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *          ),
     *     ),
     * )
     */
    public function show(Request $request, User $user)
    {
        return new UserResource($user);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Register a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               required={"name","email", "password", "password_confirmation"},
     *               @OA\Property(property="name", type="string", default="user_name", description="The name of the user"),
     *               @OA\Property(property="email", type="string", default="example@example.com"),
     *               @OA\Property(property="password", type="string", default="password"),
     *               @OA\Property(property="password_confirmation", type="string", default="password")
     *            ),
     *        ),
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="User registered successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/User"),
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="token_type", type="string", default="Bearer"),
     *          ),
     *
     *      ),
     *     @OA\Response(response="400", description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *          ),
     *     ),
     *     @OA\Response(response="422", description="Validation errors",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="fieldName", type="array", @OA\Items(type="string"))
     *              ),
     *          ),
     *     ),
     * )
     */
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'data' => $user,
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update an existing user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="User Id",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="name", type="string", default="user_name", description="The name of the user"),
     *               @OA\Property(property="password", type="string", default="password"),
     *               @OA\Property(property="password_confirmation", type="string", default="password")
     *            ),
     *        ),
     *     ),
     *
     *     @OA\Response(response="200", description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/User"),
     *          )
     *      ),
     *     @OA\Response(response="404", description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *          ),
     *     ),
     *     @OA\Response(response="400", description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *          ),
     *     ),
     *     @OA\Response(response="422", description="Validation errors",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="fieldName", type="array", @OA\Items(type="string"))
     *              ),
     *          ),
     *     ),
     * )
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        if ($password = $validated['password'] ?? null) {
            $validated['password'] = Hash::make($password);
        }

        $user->update($validated);

        return new UserResource($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/destroy/{id}",
     *     summary="Delete an existing user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="User Id",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response="200", description="Successful operation ",
     *          @OA\JsonContent(ref="#/components/schemas/User::Info")
     *      ),
     *     @OA\Response(response="404", description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string"),
     *          ),
     *     ),
     * )
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();

        return response()->noContent();
    }

}
