<?php

namespace Modules\Course\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Course\Models\Course;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(rand(3, 6));
        $title = rtrim($title, '.');

        $levels = ['beginner', 'intermediate', 'advanced'];

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(rand(3, 8), true),
            'instructor' => $this->faker->name(),
            'price' => $this->faker->randomFloat(2, 0, 500),
            'level' => $this->faker->randomElement($levels),
            'duration_hours' => $this->faker->numberBetween(1, 100),
            'cover_media_id' => null, // This would need to be set manually or via a state method
        ];
    }

    /**
     * Indicate that the course is free.
     *
     * @return Factory
     */
    public function free(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => 0,
            ];
        });
    }

    /**
     * Indicate that the course is premium.
     *
     * @return Factory
     */
    public function premium(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->randomFloat(2, 50, 500),
            ];
        });
    }

    /**
     * Set a specific level for the course.
     *
     * @param string $level
     * @return Factory
     */
    public function level(string $level): Factory
    {
        return $this->state(function (array $attributes) use ($level) {
            return [
                'level' => $level,
            ];
        });
    }

    /**
     * Set a specific duration for the course.
     *
     * @param int $hours
     * @return Factory
     */
    public function duration(int $hours): Factory
    {
        return $this->state(function (array $attributes) use ($hours) {
            return [
                'duration_hours' => $hours,
            ];
        });
    }
}
