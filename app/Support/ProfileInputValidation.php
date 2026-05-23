<?php

namespace App\Support;

class ProfileInputValidation
{
    public const NAME_PATTERN = '/^[\p{L}\s\'-]+$/u';

    public const DIGITS_PATTERN = '/^\d+$/';

    public const NAME_HTML_PATTERN = "[A-Za-zÁÉÍÓÚáéíóúÑñÜü\\s'\\-]+";

    public const DIGITS_HTML_PATTERN = '[0-9]*';

    public const PHONE_LENGTH = 10;

    public const PHONE_HTML_PATTERN = '[0-9]{10}';

    /**
     * @return list<string|\Illuminate\Validation\Rules\Unique>
     */
    public static function nameRules(): array
    {
        return ['required', 'string', 'max:255', 'regex:'.self::NAME_PATTERN];
    }

    /**
     * @return list<string>
     */
    public static function phoneRules(): array
    {
        return ['required', 'string', 'digits:'.self::PHONE_LENGTH];
    }

    /**
     * @return list<string>
     */
    public static function cardIdRules(): array
    {
        return ['required', 'string', 'regex:'.self::DIGITS_PATTERN, 'max:20'];
    }

    /**
     * @return list<string>
     */
    public static function emailRules(): array
    {
        return ['required', 'string', 'email:rfc', 'max:255'];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo puede contener letras, espacios, guiones y apostrofes.',
            'last_name.regex' => 'El apellido solo puede contener letras, espacios, guiones y apostrofes.',
            'phone.digits' => 'El telefono debe tener exactamente 10 digitos (ejemplo: 3158899001).',
            'card_id.regex' => 'El numero de identificacion solo puede contener numeros.',
            'email.email' => 'Debe ingresar un correo electronico valido.',
            'email_confirmation.email' => 'Debe ingresar un correo electronico valido.',
        ];
    }
}
