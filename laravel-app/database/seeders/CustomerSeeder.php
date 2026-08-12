<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [

            [
                'first_name' => 'کورش',
                'last_name' => 'زرتشت',
                'mobile' => '09171502501',
                'phone' => '07165325501',
                'national_code' => '2520154845',
                'birth_date' => NULL,
                'gender' => 'male',
                'province' => 'فارس',
                'city' => 'استهبان',
                'postal_code' => '5555544444',
                'address' => 'خیابان امام. کوچه شهید امامزاده. منزل امام سیزدهم',
                'customer_role_id' => '1',
                'purchase_count' => '0',
                'total_purchase_amount' => '0',
                'last_purchase_at' => NULL,
                'notes' => 'test note',
                'is_active' => '1',
            ],

            [
                'first_name' => 'داریوش',
                'last_name' => 'ایران زاده',
                'mobile' => '09171502502',
                'phone' => '07165325502',
                'national_code' => '2520154815',
                'birth_date' => NULL,
                'gender' => 'male',
                'province' => 'فارس',
                'city' => 'استهبان',
                'postal_code' => '5555544444',
                'address' => 'خیابان امام. کوچه شهید امامزاده. منزل امام سیزدهم',
                'customer_role_id' => '1',
                'purchase_count' => '0',
                'total_purchase_amount' => '0',
                'last_purchase_at' => NULL,
                'notes' => 'test note',
                'is_active' => '1',
            ],

            [
                'first_name' => 'اردشیر',
                'last_name' => 'وطن دوست',
                'mobile' => '09171502503',
                'phone' => '0716532558803',
                'national_code' => '2520154818',
                'birth_date' => NULL,
                'gender' => 'male',
                'province' => 'فارس',
                'city' => 'استهبان',
                'postal_code' => '5555544444',
                'address' => 'خیابان امام. کوچه شهید امامزاده. منزل امام سیزدهم',
                'customer_role_id' => '1',
                'purchase_count' => '0',
                'total_purchase_amount' => '0',
                'last_purchase_at' => NULL,
                'notes' => 'test note',
                'is_active' => '1',
            ],

            [
                'first_name' => 'خشایار',
                'last_name' => 'یزدان پناه',
                'mobile' => '09171502504',
                'phone' => '07165325504',
                'national_code' => '2520154885',
                'birth_date' => NULL,
                'gender' => 'male',
                'province' => 'فارس',
                'city' => 'استهبان',
                'postal_code' => '5555544444',
                'address' => 'خیابان امام. کوچه شهید امامزاده. منزل امام سیزدهم',
                'customer_role_id' => '1',
                'purchase_count' => '0',
                'total_purchase_amount' => '0',
                'last_purchase_at' => NULL,
                'notes' => 'test note',
                'is_active' => '1',
            ],

            [
                'first_name' => 'آرون',
                'last_name' => 'آریایی',
                'mobile' => '09171502505',
                'phone' => '07165325505',
                'national_code' => '2520154855',
                'birth_date' => NULL,
                'gender' => 'male',
                'province' => 'فارس',
                'city' => 'استهبان',
                'postal_code' => '5555544444',
                'address' => 'خیابان امام. کوچه شهید امامزاده. منزل امام سیزدهم',
                'customer_role_id' => '1',
                'purchase_count' => '0',
                'total_purchase_amount' => '0',
                'last_purchase_at' => NULL,
                'notes' => 'test note',
                'is_active' => '1',
            ],

            [
                'first_name' => 'آرش',
                'last_name' => 'آرین فر',
                'mobile' => '09171502506',
                'phone' => '07165325506',
                'national_code' => '2520154835',
                'birth_date' => NULL,
                'gender' => 'male',
                'province' => 'فارس',
                'city' => 'استهبان',
                'postal_code' => '5555544444',
                'address' => 'خیابان امام. کوچه شهید امامزاده. منزل امام سیزدهم',
                'customer_role_id' => '1',
                'purchase_count' => '0',
                'total_purchase_amount' => '0',
                'last_purchase_at' => NULL,
                'notes' => 'test note',
                'is_active' => '1',
            ],
        ];

        foreach ($customers as $customer) {

            Customer::updateOrCreate(

                [
                    'first_name' => $customer['first_name'],
                    'last_name' => $customer['last_name'],
                    'mobile' => $customer['mobile'],
                    'phone' => $customer['phone'],
                    'national_code' => $customer['national_code'],
                    'birth_date' => $customer['birth_date'],
                    'gender' => $customer['gender'],
                    'province' => $customer['province'],
                    'city' => $customer['city'],
                    'postal_code' => $customer['postal_code'],
                    'address' => $customer['address'],
                    'customer_role_id' => $customer['customer_role_id'],
                    'purchase_count' => $customer['purchase_count'],
                    'total_purchase_amount' => $customer['total_purchase_amount'],
                    'last_purchase_at' => $customer['last_purchase_at'],
                    'notes' => $customer['notes'],
                    'is_active' => $customer['is_active'],  
                ],

                $customer

            );

        }

    }
}