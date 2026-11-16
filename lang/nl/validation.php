<?php

return [
    'accepted' => 'Je moet :attribute accepteren.',
    'between' => [
        'numeric' => ':Attribute moet tussen :min en :max liggen.',
        'string' => ':Attribute moet tussen :min en :max tekens lang zijn.',
    ],
    'confirmed' => 'De bevestiging van :attribute komt niet overeen.',
    'current_password' => 'Het wachtwoord is onjuist.',
    'date' => ':Attribute moet een geldige datum zijn.',
    'email' => ':Attribute moet een geldig e-mailadres zijn.',
    'exists' => 'De gekozen :attribute bestaat niet.',
    'file' => ':Attribute moet een bestand zijn.',
    'in' => 'De gekozen :attribute is ongeldig.',
    'integer' => ':Attribute moet een geheel getal zijn.',
    'lowercase' => ':Attribute mag alleen kleine letters bevatten.',
    'max' => [
        'file' => ':Attribute mag niet groter zijn dan :max kilobytes.',
        'numeric' => ':Attribute mag niet groter zijn dan :max.',
        'string' => ':Attribute mag niet meer dan :max tekens bevatten.',
    ],
    'mimes' => ':Attribute moet een bestand zijn van het type: :values.',
    'min' => [
        'numeric' => ':Attribute moet minimaal :min zijn.',
        'string' => ':Attribute moet minimaal :min tekens bevatten.',
    ],
    'numeric' => ':Attribute moet een getal zijn.',
    'password' => [
        'letters' => ':Attribute moet minimaal één letter bevatten.',
        'mixed' => ':Attribute moet minimaal één hoofdletter en één kleine letter bevatten.',
        'numbers' => ':Attribute moet minimaal één cijfer bevatten.',
        'symbols' => ':Attribute moet minimaal één leesteken bevatten.',
        'uncompromised' => 'Dit :attribute is gelekt bij een datalek. Kies een ander :attribute.',
    ],
    'regex' => 'Het formaat van :attribute is ongeldig.',
    'required' => ':Attribute is verplicht.',
    'string' => ':Attribute moet tekst zijn.',
    'unique' => ':Attribute is al in gebruik.',

    // Nederlandse namen voor velden die vaak voorkomen
    'attributes' => [
        'name' => 'naam',
        'email' => 'e-mailadres',
        'password' => 'wachtwoord',
        'current_password' => 'huidig wachtwoord',
        'file' => 'bestand',
    ],
];
