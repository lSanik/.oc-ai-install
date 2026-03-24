# Схема — settings.json

## ІНСТРУКЦІЯ ДЛЯ AI

Генеруй `.claude/settings.json` — тільки для `TOOL = claude`.

---

## Логіка генерації

### permissions.deny — завжди

Додай deny для файлів з blocklist:

```
"Read(./config.php)", "Edit(./config.php)", "Write(./config.php)"
"Read(./admin/config.php)", "Edit(./admin/config.php)", "Write(./admin/config.php)"
"Read(./.env)", "Edit(./.env)", "Write(./.env)"
"Read(./.env.*)", "Edit(./.env.*)", "Write(./.env.*)"
```

Якщо під час інсталяції користувач вказав додаткові файли в blocklist — додай їх за тим самим патерном.

### Якщо `CAN_RUN_COMMANDS = no` — додатково

```
"Bash"
```

---

## Шаблон — `CAN_RUN_COMMANDS = yes`

```json
{
  "permissions": {
    "deny": [
      "Read(./config.php)",
      "Edit(./config.php)",
      "Write(./config.php)",
      "Read(./admin/config.php)",
      "Edit(./admin/config.php)",
      "Write(./admin/config.php)",
      "Read(./.env)",
      "Edit(./.env)",
      "Write(./.env)",
      "Read(./.env.*)",
      "Edit(./.env.*)",
      "Write(./.env.*)"
    ]
  }
}
```

---

## Шаблон — `CAN_RUN_COMMANDS = no`

```json
{
  "permissions": {
    "deny": [
      "Bash",
      "Read(./config.php)",
      "Edit(./config.php)",
      "Write(./config.php)",
      "Read(./admin/config.php)",
      "Edit(./admin/config.php)",
      "Write(./admin/config.php)",
      "Read(./.env)",
      "Edit(./.env)",
      "Write(./.env)",
      "Read(./.env.*)",
      "Edit(./.env.*)",
      "Write(./.env.*)"
    ]
  }
}
```

---

## Правила генерації

1. Тільки для `TOOL = claude` — Cursor не використовує цей файл
2. Не додавай порожніх секцій і коментарів у JSON
3. Не додавай інших секцій (model, hooks, env) — вони надто проектно-специфічні