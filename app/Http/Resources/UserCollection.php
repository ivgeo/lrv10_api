<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User::Collection",
 *     type="object",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
 *     @OA\Property(property="links", type="object",
 *          @OA\Property(property="first", type="string"),
 *          @OA\Property(property="last", type="string"),
 *          @OA\Property(property="prev", type="string"),
 *          @OA\Property(property="next", type="string"),
 *     ),
 *     @OA\Property(property="meta", type="object",
 *          @OA\Property(property="current_page", type="integer", default="1"),
 *          @OA\Property(property="from", type="integer", default="1"),
 *          @OA\Property(property="last_page", type="integer", default="11"),
 *          @OA\Property(property="links", type="array", @OA\Items(
 *               type="object",
 *               @OA\Property(property="url", type="string", example="http://localhost/api/users?page=1"),
 *               @OA\Property(property="label", type="string", example="1"),
 *               @OA\Property(property="active", type="boolean", example="true"),
 *          )),
 *          @OA\Property(property="path", type="string", example="http://localhost/api/users"),
 *          @OA\Property(property="per_page", type="integer", default="5"),
 *          @OA\Property(property="to", type="integer"),
 *          @OA\Property(property="total", type="integer", description="total users count"),
 *     ),
 *
 * )
 */

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
