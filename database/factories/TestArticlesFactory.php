<?php

namespace Database\Factories;

use App\Models\ModTestArticles;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestArticlesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ModTestArticles::class;

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
