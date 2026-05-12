# Admin Chatbot

Dashboard administrativo para gestionar chatbots de WhatsApp. Construido con CodeIgniter 3 + PostgreSQL.

## 🐳 Docker (recomendado)

```bash
# 1. Clonar y entrar
git clone git@github.com:saidmix01/admin_chatbot.git
cd admin_chatbot

# 2. Copiar configuración
cp .env.example .env

# 3. Levantar contenedores
docker compose up -d

# 4. La app queda en:
#    http://localhost:8080/
```

La base de datos se inicializa automáticamente con las tablas y datos semilla.

## 🛠 Stack

- **PHP** 8.2 + Apache
- **PostgreSQL** 16
- **CodeIgniter** 3
- **Bootstrap** Material Design

## 📁 Estructura

```
├── docker-compose.yml     # Orquestación de servicios
├── Dockerfile             # Imagen PHP + Apache
├── docker/
│   └── php.ini            # Configuración PHP
├── database/
│   └── init.sql           # Schema + seed data
└── application/           # Código de la app (CI3)
```

## 🔧 Variables de entorno

| Variable | Default | Descripción |
|----------|---------|-------------|
| `DB_HOST` | `db` | Host de PostgreSQL |
| `DB_USER` | `wapi` | Usuario BD |
| `DB_PASSWORD` | `wapi_secret_2026` | Contraseña BD |
| `DB_NAME` | `admin_chatbot` | Nombre BD |
| `APP_ENV` | `development` | Entorno |
| `BASE_URL` | `http://localhost:8080/` | URL base |
| `ENCRYPTION_KEY` | - | Llave de encriptación |

## 🚀 Producción

Cambiar `APP_ENV=production` y usar una `ENCRYPTION_KEY` segura.
