<?php

namespace Tests\Unit\Support;

use App\Support\ProfileInputValidation;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProfileInputValidationTest extends TestCase
{
    public function test_name_rules_reject_invalid_characters(): void
    {
        $validator = Validator::make(
            ['name' => 'Carlos123'],
            ['name' => ProfileInputValidation::nameRules()],
            ProfileInputValidation::messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_name_rules_accept_spanish_letters(): void
    {
        $validator = Validator::make(
            ['name' => "María-José O'Connor"],
            ['name' => ProfileInputValidation::nameRules()],
            ProfileInputValidation::messages()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_phone_rules_accept_colombian_mobile_number(): void
    {
        $validator = Validator::make(
            ['phone' => '3158899001'],
            ['phone' => ProfileInputValidation::phoneRules()],
            ProfileInputValidation::messages()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_phone_rules_reject_numbers_with_fewer_than_ten_digits(): void
    {
        $validator = Validator::make(
            ['phone' => '300123'],
            ['phone' => ProfileInputValidation::phoneRules()],
            ProfileInputValidation::messages()
        );

        $this->assertTrue($validator->fails());
    }

    public function test_phone_rules_reject_numbers_with_more_than_ten_digits(): void
    {
        $validator = Validator::make(
            ['phone' => '300123456789'],
            ['phone' => ProfileInputValidation::phoneRules()],
            ProfileInputValidation::messages()
        );

        $this->assertTrue($validator->fails());
    }

    public function test_phone_rules_reject_non_numeric_characters(): void
    {
        $validator = Validator::make(
            ['phone' => '300-1234567'],
            ['phone' => ProfileInputValidation::phoneRules()],
            ProfileInputValidation::messages()
        );

        $this->assertTrue($validator->fails());
    }

    public function test_card_id_rules_reject_letters(): void
    {
        $validator = Validator::make(
            ['card_id' => '12ABC'],
            ['card_id' => ProfileInputValidation::cardIdRules()],
            ProfileInputValidation::messages()
        );

        $this->assertTrue($validator->fails());
    }
}
