<?php

namespace Database\Factories;

use App\Models\Participante;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory para participantes da promoção.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participante>
 */
class ParticipanteFactory extends Factory
{
    protected $model = Participante::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake('pt_BR')->name(),
            'cpf' => fake('pt_BR')->cpf(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'telefone' => fake('pt_BR')->phoneNumber(),
            'celular' => fake('pt_BR')->cellphoneNumber(),
            'endereco' => fake('pt_BR')->streetName(),
            'numero' => (string) fake()->numberBetween(1, 9999),
            'bairro' => fake('pt_BR')->words(2, true),
            'cidade' => fake('pt_BR')->city(),
            'estado' => fake('pt_BR')->stateAbbr(),
            'cep' => fake('pt_BR')->postcode(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
