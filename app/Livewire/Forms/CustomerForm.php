<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use Livewire\Form;

class CustomerForm extends Form
{
    public ?Customer $customer = null;

    public string $name = '';
    public string $contact_name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $postcode = '';
    public string $city = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'max:100'],
            'contact_name' => ['nullable', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'max:20'],
            'address' => ['required', 'max:100'],
            'postcode' => ['required', 'regex:/^[1-9][0-9]{3} ?[A-Za-z]{2}$/'],
            'city' => ['required', 'max:50'],
        ];
    }

    protected function messages(): array
    {
        return [
            'postcode.regex' => 'Vul een geldige postcode in, bijvoorbeeld 9711 KT.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'naam',
            'contact_name' => 'contactpersoon',
            'email' => 'e-mailadres',
            'phone' => 'telefoonnummer',
            'address' => 'adres',
            'city' => 'plaats',
        ];
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
        $this->fill($customer->only(['name', 'email', 'address', 'postcode', 'city']));
        $this->contact_name = (string) $customer->contact_name;
        $this->phone = (string) $customer->phone;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->customer) {
            $this->customer->update($data);
        } else {
            Customer::create($data);
        }

        $this->reset();
    }
}
