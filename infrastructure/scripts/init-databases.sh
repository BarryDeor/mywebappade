#!/bin/bash
set -e

# Create multiple databases for different microservices
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE employee_db;
    CREATE DATABASE payroll_db;
    CREATE DATABASE attendance_db;
    CREATE DATABASE user_db;
    CREATE DATABASE benefits_db;
    CREATE DATABASE tax_db;
    CREATE DATABASE performance_db;
    CREATE DATABASE recruitment_db;
    CREATE DATABASE kong;
    
    GRANT ALL PRIVILEGES ON DATABASE employee_db TO postgres;
    GRANT ALL PRIVILEGES ON DATABASE payroll_db TO postgres;
    GRANT ALL PRIVILEGES ON DATABASE attendance_db TO postgres;
    GRANT ALL PRIVILEGES ON DATABASE user_db TO postgres;
    GRANT ALL PRIVILEGES ON DATABASE benefits_db TO postgres;
    GRANT ALL PRIVILEGES ON DATABASE tax_db TO postgres;
    GRANT ALL PRIVILEGES ON DATABASE performance_db TO postgres;
    GRANT ALL PRIVILEGES ON DATABASE recruitment_db TO postgres;
    GRANT ALL PRIVILEGES ON DATABASE kong TO postgres;
EOSQL

echo "Multiple databases created successfully!"
