CREATE TABLE /*TABLE_PREFIX*/t_item_job_attr (
    fk_i_item_id INT UNSIGNED NOT NULL,
    e_relation ENUM('HIRE', 'LOOKING'),
    e_position_type ENUM('UNDEF', 'PART', 'FULL'),
    s_salary_text TEXT NOT NULL DEFAULT '',
        PRIMARY KEY (fk_i_item_id),
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_item_job_description_attr (
    fk_i_item_id INT UNSIGNED NOT NULL,
    fk_c_locale_code CHAR(5) NOT NULL,
    s_desired_exp VARCHAR(255),
    s_studies VARCHAR(255),
    s_minimum_requirements TEXT,
    s_desired_requirements TEXT,
    s_contract VARCHAR(255),
        PRIMARY KEY (fk_i_item_id, fk_c_locale_code),
        FOREIGN KEY (fk_c_locale_code) REFERENCES /*TABLE_PREFIX*/t_locale (pk_c_code),
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';




CREATE TABLE /*TABLE_PREFIX*/t_item_job_applicant (
    pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT UNSIGNED NOT NULL,
    s_name VARCHAR(255) NOT NULL DEFAULT '',
    s_email VARCHAR(255) NOT NULL DEFAULT '',
    s_cover_letter TEXT NOT NULL DEFAULT ''
    dt_date DATETIME NOT NULL,
    i_status TINYINT DEFAULT 0,
    i_rating TINYNIT DEFAULT 0,

        PRIMARY KEY (pk_i_id),
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';


CREATE TABLE /*TABLE_PREFIX*/t_item_job_file (
    pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_applicant_id INT UNSIGNED NOT NULL,
    dt_date DATETIME NOT NULL,
    s_name VARCHAR(255) NOT NULL DEFAULT '',

        PRIMARY KEY (pk_i_id),
        FOREIGN KEY (fk_i_applicant_id) REFERENCES /*TABLE_PREFIX*/t_item_job_applicants (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_item_job_log (
    pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT UNSIGNED NOT NULL,
    fk_i_applicant_id INT UNSIGNED NOT NULL,
    fk_i_admin_id INT UNSIGNED NOT NULL,
    dt_date DATETIME NOT NULL,
    i_status TINYINT DEFAULT 0,
     TINYNIT DEFAULT 0,

        PRIMARY KEY (pk_i_id),
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id),
        FOREIGN KEY (fk_i_applicant_id) REFERENCES /*TABLE_PREFIX*/t_item_job_applicants (pk_i_id)
        FOREIGN KEY (fk_i_admin_id) REFERENCES /*TABLE_PREFIX*/t_admin (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';