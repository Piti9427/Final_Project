-- PostgreSQL version of newcompany.sql
-- Note: This is a simplified version - you may need to adjust data types

CREATE TABLE assessments (
    id SERIAL PRIMARY KEY,
    applications_id INTEGER,
    assessor_id INTEGER,
    total_score DECIMAL(5,2),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE authorize (
    user_no SERIAL PRIMARY KEY,
    user_login VARCHAR(100),
    branch_no VARCHAR(50),
    role VARCHAR(50)
);

CREATE TABLE company_images (
    id SERIAL PRIMARY KEY,
    image_name VARCHAR(100),
    image_type VARCHAR(100),
    image_data BYTEA,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE company_info (
    id SERIAL PRIMARY KEY,
    company_name VARCHAR(255),
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(100),
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE inventory (
    id SERIAL PRIMARY KEY,
    item_name VARCHAR(255),
    quantity INTEGER,
    price DECIMAL(10,2),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE logos (
    id SERIAL PRIMARY KEY,
    logo_name VARCHAR(100),
    logo_type VARCHAR(100),
    logo_data BYTEA,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE press_release (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    publish_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE scholarship_applications (
    id SERIAL PRIMARY KEY,
    scholarship_id INTEGER,
    student_id VARCHAR(50),
    prefix_th VARCHAR(20),
    first_name_th VARCHAR(100),
    last_name_th VARCHAR(100),
    faculty VARCHAR(100),
    branch_no VARCHAR(50),
    year_level INTEGER,
    gpa DECIMAL(3,2),
    status VARCHAR(50),
    user_no INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE scholarships (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    amount DECIMAL(10,2),
    deadline DATE,
    requirements TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE suppliers (
    id SERIAL PRIMARY KEY,
    supplier_name VARCHAR(255),
    contact_person VARCHAR(100),
    phone VARCHAR(50),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    user_no SERIAL PRIMARY KEY,
    user_email VARCHAR(100) UNIQUE,
    user_name VARCHAR(100),
    user_login VARCHAR(100) UNIQUE,
    user_password VARCHAR(100),
    image_name VARCHAR(100),
    image_type VARCHAR(100),
    image_data BYTEA,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO users (user_email, user_name, user_login, user_password, image_name, image_type) VALUES
('admin@gmail.com', 'admin', 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'logormutt.png', 'image/png'),
('user@gmail.com', 'user', 'user', 'e10adc3949ba59abbe56e057f20f883e', 'logormutt.png', 'image/png');

INSERT INTO scholarships (title, description, amount, deadline, requirements) VALUES
('ทุนการศึกษาประจำปี 2567', 'ทุนสำหรับนักศึกษาที่มีผลการเรียนดี', 10000.00, '2024-12-31', 'GPA 3.00 ขึ้นไป');

INSERT INTO authorize (user_login, branch_no, role) VALUES
('admin', 'CS', 'admin'),
('user', 'CS', 'user');
