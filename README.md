# Cupom Hexagonal

Projeto simples de **cupons de desconto**, criado com o objetivo de **aprender e praticar Arquitetura Hexagonal** (Ports and Adapters) usando Laravel.

> Este é um projeto de estudo. O foco não é ter muitas funcionalidades, e sim organizar o código de forma que as regras de negócio fiquem isoladas de frameworks, banco de dados e outros detalhes de infraestrutura.

## Objetivo

- Entender na prática os conceitos de **domínio**, **portas** (ports) e **adaptadores** (adapters).
- Manter as regras de negócio dos cupons independentes do Laravel e do banco de dados.
- Facilitar a troca de tecnologias (ex.: banco, HTTP, CLI) sem alterar o núcleo da aplicação.
- Escrever testes do domínio sem depender de infraestrutura.

## Arquitetura Hexagonal em resumo

```
          Adaptadores de entrada              Adaptadores de saída
        (HTTP, CLI, testes...)              (PostgreSQL, memória...)
                  │                                   ▲
                  ▼                                   │
          ┌──────────────┐                    ┌──────────────┐
          │ Portas de    │                    │ Portas de    │
          │ entrada      │                    │ saída        │
          └──────┬───────┘                    └──────▲───────┘
                 │                                   │
                 ▼                                   │
          ┌──────────────────────────────────────────┴──┐
          │        Aplicação (casos de uso)             │
          │                                             │
          │        Domínio (entidades e regras)         │
          └─────────────────────────────────────────────┘
```

- **Domínio**: entidades e regras de negócio do cupom (ex.: validade, valor do desconto, limite de uso).
- **Aplicação**: casos de uso que orquestram o domínio (ex.: criar cupom, aplicar cupom).
- **Portas**: interfaces que definem como o núcleo conversa com o mundo externo.
- **Adaptadores**: implementações concretas das portas (controllers, repositórios Eloquent, etc.).

## Diagramas

Os diagramas abaixo mostram a arquitetura planejada. Nem todas as classes existem ainda (veja `BACKEND_TODO.md`).

### Arquitetura (camadas, portas e adaptadores)

```mermaid
flowchart LR
    Client([Cliente HTTP])

    subgraph Infra_In["Infrastructure - entrada (adaptador)"]
        Controller["CouponController"]
        Response["CouponResponse"]
    end

    subgraph Core["Núcleo"]
        subgraph App["Application"]
            Create["CreateCouponUseCase"]
            Apply["ApplyCouponUseCase"]
        end

        subgraph Domain["Domain"]
            Coupon["Coupon (entidade)"]
            Port{{"CouponRepositoryInterface (porta)"}}
            Ex["Exceções de domínio<br/>NotFound / AlreadyExists / NotUsable"]
        end
    end

    subgraph Infra_Out["Infrastructure - saída (adaptadores)"]
        DbRepo["DatabaseCouponRepository"]
        MemRepo["InMemoryCouponRepository"]
        Model["CouponModel (Eloquent)"]
    end

    DB[("Banco de dados")]

    Client --> Controller
    Controller --> Create
    Controller --> Apply
    Controller --> Response
    Create --> Coupon
    Apply --> Coupon
    Create --> Port
    Apply --> Port
    Coupon --> Ex

    DbRepo -. implementa .-> Port
    MemRepo -. implementa .-> Port
    DbRepo --> Model
    Model --> DB
```

A regra de dependência é que as setas sempre apontam para o núcleo. O `Domain` não importa nada de fora, e a `Infrastructure` implementa as portas.

### Fluxo de `POST /coupons/{code}/apply`

```mermaid
sequenceDiagram
    actor C as Cliente
    participant Ctrl as CouponController
    participant UC as ApplyCouponUseCase
    participant Repo as CouponRepositoryInterface
    participant DB as DatabaseCouponRepository
    participant E as Coupon

    C->>Ctrl: POST /coupons/{code}/apply (amount)
    Ctrl->>UC: execute(code, amount, now)
    UC->>Repo: findByCode(code)
    Repo->>DB: findByCode(code)
    DB-->>UC: Coupon ou null

    alt cupom não existe
        UC-->>Ctrl: CouponNotFoundException
        Ctrl-->>C: 404
    else cupom existe
        UC->>E: calculateDiscount(amount, now)
        alt expirado ou esgotado
            E-->>UC: CouponNotUsableException
            UC-->>Ctrl: exceção sobe
            Ctrl-->>C: 422
        else pode ser usado
            E-->>UC: desconto
            UC->>E: registerUsage(now)
            UC->>Repo: save(coupon)
            Repo->>DB: save(coupon)
            UC-->>Ctrl: desconto
            Ctrl-->>C: 200 (CouponResponse)
        end
    end
```

## Stack

- PHP / Laravel
- PostgreSQL
- Nginx
- Docker Compose

## Como rodar

```sh
docker compose up -d --build
```

A aplicação ficará disponível em [http://localhost:8000](http://localhost:8000) (porta configurável via `APP_PORT`).
