<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Office;

class OfficeFactory extends Factory
{
    protected $model = Office::class;

    public function definition()
    {
        return [
            'company_id'        => 1,
            'name'              => $this->faker->company,
            'parent_office_id'  => null,
            'office_lead'       => null,
        ];
    }
    
    /**
     * Configure the model factory to set an office lead.
     *
     * @return $this
     */
    public function withLead()
    {
        return $this->state(function (array $attributes) {
            // Try to find a user that belongs to the company
            $user = User::inRandomOrder()->first();
            
            return [
                'office_lead' => $user ? $user->id : null,
            ];
        });
    }
}