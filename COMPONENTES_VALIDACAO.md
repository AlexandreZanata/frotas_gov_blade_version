# Componentes de Validação Reutilizáveis

Este documento descreve como usar os componentes de validação reutilizáveis criados para o sistema.

## Componentes Disponíveis

### 1. Input CPF (`<x-input-cpf>`)

Valida CPF com máscara automática e validação de dígitos verificadores.

**Uso:**
```blade
<x-input-cpf 
    id="cpf" 
    name="cpf" 
    :value="old('cpf', $user->cpf ?? '')" 
    class="mt-1 block w-full" 
    required 
/>
```

**Recursos:**
- Máscara automática: 000.000.000-00
- Validação de dígitos verificadores
- Verifica CPFs inválidos (sequências repetidas)
- Mensagem de erro em tempo real

---

### 2. Input Placa (`<x-input-plate>`)

Valida placas de veículos (padrão antigo e Mercosul).

**Uso:**
```blade
<x-input-plate 
    id="plate" 
    name="plate" 
    :value="old('plate', $vehicle->plate ?? '')" 
    class="mt-1 block w-full" 
    required 
/>
```

**Recursos:**
- Suporta padrão antigo: ABC-1234
- Suporta padrão Mercosul: ABC-1D23
- Converte automaticamente para maiúsculas
- Identifica o tipo de placa

---

### 3. Input CNH (`<x-input-cnh>`)

Valida número de CNH com verificação de dígitos.

**Uso:**
```blade
<x-input-cnh 
    id="cnh" 
    name="cnh" 
    :value="old('cnh', $user->cnh ?? '')" 
    class="mt-1 block w-full" 
/>
```

**Recursos:**
- Validação de 11 dígitos
- Verifica dígitos verificadores da CNH
- Impede sequências repetidas

---

### 4. Input Telefone (`<x-input-phone>`)

Valida e formata números de telefone (fixo e celular).

**Uso:**
```blade
<x-input-phone 
    id="phone" 
    name="phone" 
    :value="old('phone', $user->phone ?? '')" 
    class="mt-1 block w-full" 
/>
```

**Recursos:**
- Máscara automática para fixo: (00) 0000-0000
- Máscara automática para celular: (00) 00000-0000
- Validação de DDD

---

### 5. Input Data Validada (`<x-input-date-validated>`)

Valida datas com regras específicas (ex: CNH vencida).

**Uso:**
```blade
<x-input-date-validated 
    id="cnh_expiration_date" 
    name="cnh_expiration_date" 
    :value="old('cnh_expiration_date', $user->cnh_expiration_date ?? '')" 
    class="mt-1 block w-full" 
    min-date="today" 
/>
```

**Parâmetros:**
- `min-date`: Define data mínima ("today", "none", ou data específica)

**Recursos:**
- Alerta de CNH próxima ao vencimento (30 dias)
- Erro para CNH vencida
- Validação de datas futuras/passadas

---

## Parâmetros Comuns

Todos os componentes aceitam os seguintes parâmetros:

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|--------|-----------|
| `id` | string | - | ID do campo |
| `name` | string | - | Nome do campo (para submit) |
| `value` | string | '' | Valor inicial |
| `required` | boolean | false | Campo obrigatório |
| `disabled` | boolean | false | Campo desabilitado |
| `class` | string | - | Classes CSS adicionais |

## Exemplo Completo

```blade
<form method="POST" action="{{ route('users.store') }}">
    @csrf
    
    <!-- CPF -->
    <div>
        <x-input-label for="cpf" value="CPF *" />
        <x-input-cpf id="cpf" name="cpf" :value="old('cpf')" required />
        <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
    </div>

    <!-- Telefone -->
    <div>
        <x-input-label for="phone" value="Telefone" />
        <x-input-phone id="phone" name="phone" :value="old('phone')" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <!-- CNH -->
    <div>
        <x-input-label for="cnh" value="CNH" />
        <x-input-cnh id="cnh" name="cnh" :value="old('cnh')" />
        <x-input-error :messages="$errors->get('cnh')" class="mt-2" />
    </div>

    <!-- Data de Validade da CNH -->
    <div>
        <x-input-label for="cnh_expiration_date" value="Validade da CNH" />
        <x-input-date-validated 
            id="cnh_expiration_date" 
            name="cnh_expiration_date" 
            :value="old('cnh_expiration_date')" 
            min-date="today" 
        />
        <x-input-error :messages="$errors->get('cnh_expiration_date')" class="mt-2" />
    </div>

    <x-primary-button>Salvar</x-primary-button>
</form>
```

## Validação no Backend

Lembre-se de sempre validar os dados no backend também:

```php
$request->validate([
    'cpf' => ['required', 'string', 'max:14', 'unique:users,cpf'],
    'phone' => ['nullable', 'string', 'max:20'],
    'cnh' => ['nullable', 'string', 'max:11'],
    'cnh_expiration_date' => ['nullable', 'date', 'after:today'],
    'plate' => ['required', 'string', 'max:8', 'unique:vehicles,plate'],
]);
```

## Tecnologias Utilizadas

- **Alpine.js**: Para reatividade e validação em tempo real
- **Blade Components**: Para reutilização de código
- **Tailwind CSS**: Para estilização

## Contribuindo

Para adicionar novos componentes de validação, siga o padrão:

1. Crie o arquivo em `resources/views/components/`
2. Use Alpine.js para validação em tempo real
3. Retorne mensagens de erro claras
4. Documente o uso aqui

