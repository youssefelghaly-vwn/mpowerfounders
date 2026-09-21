<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // Factory users are activated by default; the pending state is
            // the interesting one, so it gets an explicit state below.
            'status' => User::STATUS_ACTIVE,
            'activated_at' => now(),
        ];
    }

    /** Signed up but not yet reviewed — no role, cannot sign in. */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => User::STATUS_PENDING,
            'activated_at' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => User::STATUS_SUSPENDED]);
    }

    /** An activated client: active, holding the 'client' role. */
    public function client(): static
    {
        return $this->afterCreating(function (User $user) {
            $role = Role::firstOrCreate(
                ['slug' => 'client'],
                ['name' => 'Client', 'description' => 'Uploads projects and follows them through production.'],
            );

            $user->roles()->syncWithoutDetaching([$role->id]);
        });
    }

    /** Staff member holding the given role slug (created if missing). */
    public function withRole(string $slug): static
    {
        return $this->afterCreating(function (User $user) use ($slug) {
            $role = Role::firstOrCreate(['slug' => $slug], ['name' => Str::headline($slug)]);

            $user->roles()->syncWithoutDetaching([$role->id]);
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
