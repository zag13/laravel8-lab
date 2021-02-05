<?php

namespace Database\Factories;

use App\Models\ModTestES;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModTestESFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ModTestES::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'title' => $this->faker->title,
            'content' => $this->faker->text
        ];
    }
}
