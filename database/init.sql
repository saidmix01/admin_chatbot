-- Admin Chatbot - Database Schema
-- PostgreSQL 16

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================================
-- USERS & AUTH
-- ============================================================

CREATE TABLE users (
    us_id SERIAL PRIMARY KEY,
    us_status INTEGER DEFAULT 1,  -- 1: Active, 0: Inactive
    us_name VARCHAR(100) NOT NULL,
    us_email VARCHAR(150) UNIQUE NOT NULL,
    pro_id INTEGER,
    us_password TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE profiles (
    pro_id SERIAL PRIMARY KEY,
    pro_status INTEGER DEFAULT 1,  -- 1: Active, 0: Inactive
    pro_description VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD CONSTRAINT fk_users_profile FOREIGN KEY (pro_id) REFERENCES profiles(pro_id) ON DELETE SET NULL;

-- ============================================================
-- MENUS & PERMISSIONS
-- ============================================================

CREATE TABLE menus (
    men_id SERIAL PRIMARY KEY,
    men_status INTEGER DEFAULT 1,  -- 1: Active, 0: Inactive
    men_description VARCHAR(100) NOT NULL,
    men_url VARCHAR(200),
    men_icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu_profile (
    mp_id SERIAL PRIMARY KEY,
    pro_id INTEGER NOT NULL REFERENCES profiles(pro_id) ON DELETE CASCADE,
    men_id INTEGER NOT NULL REFERENCES menus(men_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (pro_id, men_id)
);

-- ============================================================
-- STORES
-- ============================================================

CREATE TABLE stores (
    sto_id SERIAL PRIMARY KEY,
    sto_status INTEGER DEFAULT 1,
    us_id INTEGER REFERENCES users(us_id) ON DELETE SET NULL,
    sto_name VARCHAR(150) NOT NULL,
    sto_email VARCHAR(255),
    sto_direction VARCHAR(255),
    sto_phone VARCHAR(20),
    sto_wellcome_message TEXT,
    sto_starters TEXT DEFAULT '[]',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_store (
    user_store_id SERIAL PRIMARY KEY,
    us_id INTEGER NOT NULL REFERENCES users(us_id) ON DELETE CASCADE,
    sto_id INTEGER NOT NULL REFERENCES stores(sto_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- LISTS & OPTIONS (productos/servicios)
-- ============================================================

CREATE TABLE lists (
    lis_id SERIAL PRIMARY KEY,
    lis_status INTEGER DEFAULT 1,
    lis_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE options (
    opt_id SERIAL PRIMARY KEY,
    lis_id INTEGER NOT NULL REFERENCES lists(lis_id) ON DELETE CASCADE,
    opt_status INTEGER DEFAULT 1,
    opt_description VARCHAR(150) NOT NULL,
    opt_price NUMERIC(10, 2),
    opt_qty INTEGER,
    opt_order INTEGER,
    opt_more_information VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- CHAT QUESTIONS & FLOWS
-- ============================================================

CREATE TABLE chat_questions (
    chq_id SERIAL PRIMARY KEY,
    chq_status INTEGER NOT NULL DEFAULT 1,
    chq_response INTEGER DEFAULT NULL,     -- Solo para tipo yes/no
    chq_type INTEGER NOT NULL,             -- 1: List, 2: Yes/No, 3: Text
    chq_order INTEGER NOT NULL,
    chq_text TEXT NOT NULL,
    chq_lists INTEGER DEFAULT NULL,        -- FK a lists (para tipo "list")
    chq_parent INTEGER DEFAULT NULL,       -- ID de pregunta padre
    FOREIGN KEY (chq_parent) REFERENCES chat_questions(chq_id) ON DELETE CASCADE
);

-- ============================================================
-- QUESTIONS & ANSWERS (formularios)
-- ============================================================

CREATE TABLE questions (
    que_id SERIAL PRIMARY KEY,
    que_order INTEGER NOT NULL,
    que_question TEXT NOT NULL,
    que_parent INTEGER DEFAULT 0,
    us_id INTEGER NOT NULL REFERENCES users(us_id) ON DELETE CASCADE,
    que_create_at DATE DEFAULT CURRENT_DATE
);

CREATE TABLE answer (
    ans_id SERIAL PRIMARY KEY,
    ans_order INTEGER NOT NULL,
    ans_text TEXT NOT NULL,
    que_id INTEGER NOT NULL REFERENCES questions(que_id) ON DELETE CASCADE,
    us_id INTEGER NOT NULL REFERENCES users(us_id) ON DELETE CASCADE,
    ans_create_at DATE DEFAULT CURRENT_DATE
);

-- ============================================================
-- STATISTICS
-- ============================================================

CREATE TABLE statistics (
    sta_id SERIAL PRIMARY KEY,
    sto_id INTEGER REFERENCES stores(sto_id) ON DELETE CASCADE,
    sta_type VARCHAR(50),
    sta_value TEXT,
    sta_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- INDEXES
-- ============================================================

CREATE INDEX idx_users_email ON users(us_email);
CREATE INDEX idx_stores_user ON stores(us_id);
CREATE INDEX idx_chat_questions_parent ON chat_questions(chq_parent);
CREATE INDEX idx_options_list ON options(lis_id);
CREATE INDEX idx_questions_user ON questions(us_id);
CREATE INDEX idx_answer_question ON answer(que_id);
CREATE INDEX idx_statistics_store ON statistics(sto_id);

-- ============================================================
-- BOT NOTIFICATIONS (background queue)
-- ============================================================

CREATE TABLE IF NOT EXISTS bot_notifications (
    bn_id SERIAL PRIMARY KEY,
    us_id INTEGER NOT NULL,
    bn_type VARCHAR(64) NOT NULL,
    bn_status VARCHAR(24) NOT NULL DEFAULT 'pending',
    bn_payload JSONB NOT NULL DEFAULT '{}'::jsonb,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL
);

CREATE INDEX IF NOT EXISTS idx_bot_notifications_us_status_created
    ON bot_notifications (us_id, bn_status, created_at DESC);

-- ============================================================
-- SEED DATA
-- ============================================================

-- Default admin profile
INSERT INTO profiles (pro_status, pro_description) VALUES (1, 'Administrador');
INSERT INTO profiles (pro_status, pro_description) VALUES (1, 'Cliente');

-- Default menus
INSERT INTO menus (men_status, men_description, men_url, men_icon) VALUES
(1, 'Dashboard', 'home', 'fa fa-home'),
(1, 'Chatbot', 'chat_configuration', 'fa fa-comments'),
(1, 'Preguntas', 'chat_configuration/questions', 'fa fa-question-circle'),
(1, 'Tiendas', 'stores', 'fa fa-store'),
(1, 'Listas', 'list_manage', 'fa fa-list'),
(1, 'Usuarios', 'users', 'fa fa-users'),
(1, 'Perfiles', 'profiles', 'fa fa-id-badge'),
(1, 'Menús', 'menu', 'fa fa-bars'),
(1, 'Pagos', 'checkout', 'fa fa-credit-card');
