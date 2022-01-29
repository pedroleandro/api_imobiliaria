@component('mail::message')

# Novo Contato

Contato : {{ $name }} < {{ $email }}

Telefone: {{ $cell }}

{{ $message}}

@endcomponent
