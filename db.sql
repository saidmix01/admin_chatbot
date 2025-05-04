
CREATE TABLE users (
    us_id SERIAL PRIMARY KEY,
    us_status INT, -- 1: Active, 0: Inactive
    us_name VARCHAR(100) NOT NULL,
    us_email VARCHAR(150) UNIQUE NOT NULL,
    pro_id INTEGER, -- Asume que está relacionado con una tabla de perfiles
    us_password TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE profiles (
    pro_id SERIAL PRIMARY KEY,
    pro_status INT, -- 1: Active, 0: Inactive
    pro_description VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menus (
    men_id SERIAL PRIMARY KEY,
    men_status INT, -- 1: Active, 0: Inactive
    men_description VARCHAR(100) NOT NULL, -- Nombre del menú
    men_url VARCHAR(200), -- URL asociada
    men_icon VARCHAR(100), -- Clase o nombre del ícono (ej: "fa fa-user")
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu_profile (
    mp_id SERIAL PRIMARY KEY,
    pro_id INTEGER NOT NULL,
    men_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_mp_profile FOREIGN KEY (pro_id) REFERENCES profiles(pro_id) ON DELETE CASCADE,
    CONSTRAINT fk_mp_menu FOREIGN KEY (men_id) REFERENCES menus(men_id) ON DELETE CASCADE,
    
    CONSTRAINT uc_menu_profile UNIQUE (pro_id, men_id)
);

CREATE TABLE lists (
    lis_id SERIAL PRIMARY KEY,
    lis_status INT, -- 1: Active, 0: Inactive
    lis_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE options (
    opt_id SERIAL PRIMARY KEY,
    lis_id INTEGER NOT NULL,
    opt_status INT, -- 1: Active, 0: Inactive
    opt_description VARCHAR(150) NOT NULL,
    opt_price NUMERIC(10, 2),
    opt_qty INTEGER,
    opt_order INTEGER,
    opt_more_information VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_options_list FOREIGN KEY (lis_id) REFERENCES lists(lis_id) ON DELETE CASCADE
);

CREATE TABLE questions (
    chq_id SERIAL PRIMARY KEY,
    chq_status INT, -- 1: Active, 0: Deactive
    chq_response INT, -- 1: Yes, 0: No
    chq_type SMALLINT, -- 1: List, 2: Yes/No, 3: Text
    chq_order INTEGER,
    chq_text VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE stores (
    sto_id SERIAL PRIMARY KEY,
    sto_status INT, -- 1: Active, 0: Inactive
    us_id INTEGER, -- ID del propietario (usuario)
    sto_name VARCHAR(150) NOT NULL,
    sto_email VARCHAR(255),
    sto_direction VARCHAR(255),
    sto_phone VARCHAR(20),
    sto_wellcome_message TEXT, -- Mensaje de bienvenida
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_stores_user FOREIGN KEY (us_id) REFERENCES users(us_id) ON DELETE SET NULL
);


CREATE TABLE chat_questions (
    chq_id SERIAL PRIMARY KEY,
    chq_status INT NOT NULL,
    chq_response INT DEFAULT NULL,  -- Solo para el formulario "yes_no"
    chq_type INT NOT NULL,
    chq_order INT NOT NULL,
    chq_text TEXT NOT NULL,
    chq_lists INT DEFAULT NULL,  -- Solo para el formulario "list"
    chq_parent INT DEFAULT NULL, -- ID de la pregunta padre, si aplica
    FOREIGN KEY (chq_parent) REFERENCES chat_questions(chq_id) ON DELETE CASCADE
);



CREATE TABLE user_store (
    user_store_id SERIAL PRIMARY KEY,  -- Identificador único de la relación
    us_id INT NOT NULL,              -- ID del usuario (clave foránea)
    sto_id INT NOT NULL,             -- ID de la tienda (clave foránea)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Fecha de creación de la relación
    FOREIGN KEY (us_id) REFERENCES users(us_id) ON DELETE CASCADE,  -- Relación con la tabla users
    FOREIGN KEY (sto_id) REFERENCES stores(sto_id) ON DELETE CASCADE  -- Relación con la tabla stores
);

CREATE TABLE questions (
    que_id SERIAL PRIMARY KEY,
    que_order INT NOT NULL,
    que_question TEXT NOT NULL,
  	que_parent INT DEFAULT 0,
  	us_id INT NOT NULL,
  	que_create_at DATE
);

CREATE TABLE answer (
    ans_id SERIAL PRIMARY KEY,
    ans_order INT NOT NULL,
    ans_text TEXT NOT NULL,
  	que_id INT NOT NULL,
  	us_id INT NOT NULL,
  	ans_create_at DATE
);