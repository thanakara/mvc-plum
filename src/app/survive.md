# Refactor Notes — ORM Migration

## Dependency Audit

| Class | Extends / Uses | Via | Status |
|---|---|---|---|
| `ViewModel` | `Model` | `App::proxy()` | ✅ kept |
| `UsersModel` | `Model` | `App::proxy()` | 🗑️ dead |
| `AccountsModel` | `Model` | `App::proxy()` | 🗑️ dead |
| `DBALAccountService` | `UsersModel` + `AccountsModel` | — | 🗑️ dead |
| `PDOAccountService` | `UsersModel` + `AccountsModel` | — | 🗑️ dead |

---

## Delete List

> All replaced by the ORM path — safe to remove.

| File | Replaced by |
|---|---|
| `src/app/Services/DBALAccountService.php` | `ORMAccountService` |
| `src/app/Services/PDOAccountService.php` | `ORMAccountService` |
| `src/app/Models/UsersModel.php` | `Entities/User` |
| `src/app/Models/AccountsModel.php` | `Entities/Account` |

---

## Surviving Architecture

### Write path — `AccountsController::store()`

```
AccountsController
└── ORMAccountService
      └── EntityManagerInterface        (container binding)
            └── User + Account entities (cascade: persist)
```

### Read path — `HomeController::active()`

```
HomeController
└── ViewModel
      └── Model
            └── App::proxy() → DBALDatabase
                  └── plum.active_users  (SQL view, DBAL QueryBuilder)
```

---

## Why ViewModel stays on DBAL

`plum.active_users` is a raw SQL view — there is no Doctrine entity mapped to it, and creating one just for a read-only projection would be the wrong tool. DBAL for reads, ORM for writes is a legitimate and intentional split.

`App::proxy()` survives **solely** to serve `ViewModel`. It is the only remaining thread to pull in a future second-pass refactor: bind `DBALDatabase` in the container, inject it into `Model` via constructor, and `App` becomes a pure HTTP kernel with no static state.