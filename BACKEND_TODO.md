# Backend: o que falta fazer

Projeto de estudo de arquitetura hexagonal com cupons.

Endpoints alvo:

```text
POST /coupons
POST /coupons/{code}/apply
```

## Já feito

- [x] `app/Domain/Coupon.php` (entidade com as regras)
- [x] `app/Domain/CouponNotUsableException.php`
- [x] `app/Domain/CouponAlreadyExistsException.php`
- [x] `app/Domain/CouponRepositoryInterface.php` (porta de saída)
- [x] `app/Application/CreateCouponUseCase.php`

## Domain

- [x] `CouponNotFoundException` em `app/Domain` (com `forCode($code)`)
- [x] Getters necessários em `Coupon` para o repositório gravar: `getPercentage()`, `getExpiresAt()`, `getUsageLimit()`, `getTimesUsed()`

## Application

- [x] `ApplyCouponUseCase` em `app/Application`
  - [x] Buscar o cupom com `findByCode`
  - [x] Lançar `CouponNotFoundException` se não existir
  - [x] Chamar `calculateDiscount($amount, $now)` antes de `registerUsage($now)`
  - [x] Salvar com `save` depois de registrar o uso
  - [x] Retornar o valor do desconto

## Infrastructure: persistência (adaptador de saída)

- [x] Migration da tabela `coupons`
  - [x] `code` com índice `unique`
  - [x] `percentage`
  - [x] `expires_at`
  - [x] `usage_limit` (nullable)
  - [x] `times_used` (default 0)
  - [x] timestamps
- [x] Model Eloquent `CouponModel` (só na infraestrutura, o domínio não pode conhecê-lo)
- [x] `DatabaseCouponRepository` implementando `CouponRepositoryInterface`
  - [x] `findByCode`: converter o registro em `Coupon`
  - [x] `save`: converter `Coupon` em registro (criar ou atualizar)
- [x] `InMemoryCouponRepository` (adaptador para testes)

## Infrastructure: HTTP (adaptador de entrada)

- [x] `CouponController`
  - [x] `POST /coupons` chama `CreateCouponUseCase`
  - [x] `POST /coupons/{code}/apply` chama `ApplyCouponUseCase`
- [x] Form Requests de validação (formato dos dados de entrada)
- [x] `CouponResponse` (formato da resposta)
- [x] Rotas em `routes/api.php`
- [x] Traduzir exceções de domínio em respostas HTTP
  - [x] `CouponNotFoundException` → 404
  - [x] `CouponAlreadyExistsException` → 409
  - [x] `CouponNotUsableException` → 422

## Configuração

- [x] `AppServiceProvider`: ligar `CouponRepositoryInterface` a `DatabaseCouponRepository` no container
- [x] Instalar o roteamento de API do Laravel, se ainda não existir `routes/api.php` (`php artisan install:api`)
- [x] Rodar as migrations no ambiente Docker

## Testes

- [x] Testes unitários de `Coupon` (expirado, limite de uso, desconto, exceções), sem Laravel
- [x] Testes de `CreateCouponUseCase` com `InMemoryCouponRepository` (inclui código duplicado)
- [x] Testes de `ApplyCouponUseCase` com `InMemoryCouponRepository` (inclui cupom inexistente, expirado e esgotado)
- [x] Testes de feature dos dois endpoints

## Verificação final

- [x] Conferir que `app/Domain` e `app/Application` não importam nada de `Illuminate\*`
- [x] Rodar toda a suíte de testes
