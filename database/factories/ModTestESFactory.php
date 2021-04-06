<?php

namespace Database\Factories;

use App\Models\TestEsModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModTestESFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TestEsModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'occupation' => $this->faker->title,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'country' => $this->faker->country,
            'address' => $this->faker->address,
            'bank' => $this->faker->streetName,
            'company' => $this->faker->company,
            'sentence' => $this->faker->words(10, true),
            'paragraph' => $this->faker->paragraphs(3, true),
            'text' => $this->faker->realText(333)
        ];
    }
}
