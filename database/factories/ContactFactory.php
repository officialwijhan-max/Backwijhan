<?php

namespace Database\Factories;

use App\Enums\ContactStatus;
use App\Models\Contact;
use App\Support\EngagementOptions;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'company' => $this->faker->optional()->company(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->optional()->e164PhoneNumber(),
            'subject' => $this->faker->optional()->sentence(4),
            'message' => $this->faker->paragraph(),
            'project_type' => $this->faker->randomElement(EngagementOptions::PROJECT_TYPES_EN),
            'budget_range' => $this->faker->randomElement(EngagementOptions::BUDGET_RANGES_EN),
            'locale' => $this->faker->randomElement(['en', 'ar']),
            'status' => ContactStatus::New,
            'source' => 'website',
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'submitted_at' => now(),
        ];
    }
}
