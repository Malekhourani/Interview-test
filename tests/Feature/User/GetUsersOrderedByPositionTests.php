<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\User;
use Tests\TestCase;

class GetUsersOrderedByPositionTests extends TestCase
{
    /** @test */
    public function test_GetUsersOrderedByPosition_ShouldThrowValidationException_WhenNQueryStringIsNegative()
    {
        $result = $this->get('api/v1/users/1/karma-position?n=-2');

        $result->assertUnprocessable();
    }

    /** @test */
    public function test_GetUsersOrderedByPosition_ShouldThrowValidationException_WhenNQueryStringIsString()
    {
        $result = $this->get('api/v1/users/1/karma-position?n=hello');

        $result->assertUnprocessable();
    }

    /** @test */
    public function test_GetUsersOrderedByPosition_ShouldThrowNotFoundException_WhenUserIdIsNotValid()
    {
        $user = User::factory()->has(
            Image::factory()
        )->create();

        $invalidId = $user->id + 1;

        $result = $this->get("api/v1/users/$invalidId/karma-position?n=2");

        $result->assertNotFound();
    }

    /** @test */
    public function test_GetUsersOrderedByPosition_ShouldReturn200_WhenUserIdIsValid()
    {
        $image = Image::factory();

        $imageModel = Image::first();

        $user = User::factory()->has($image)->create(['image_id' => $imageModel->id]);

        $invalid = $user->id;

        $result = $this->get("api/v1/users/$invalid/karma-position?n=2");

        $result->assertOk();
        $result->assertJsonStructure([
            '*' => [
                'id', 'username', 'image_url', 'karma_score', 'position'
            ]
        ], $result->original->toArray());
    }
}
