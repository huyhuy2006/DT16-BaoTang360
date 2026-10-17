CREATE DATABASE IF NOT EXISTS baotang_ao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE baotang_ao;

CREATE TABLE users (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE, email_verified_at TIMESTAMP NULL, password VARCHAR(255) NOT NULL, role ENUM('admin','editor','visitor') NOT NULL DEFAULT 'visitor', is_active TINYINT(1) NOT NULL DEFAULT 1, remember_token VARCHAR(100) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE password_reset_tokens (email VARCHAR(255) PRIMARY KEY, token VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL);
CREATE TABLE personal_access_tokens (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, tokenable_type VARCHAR(255) NOT NULL, tokenable_id BIGINT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(64) NOT NULL UNIQUE, abilities TEXT NULL, last_used_at TIMESTAMP NULL, expires_at TIMESTAMP NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, INDEX idx_pat_tokenable(tokenable_type, tokenable_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE languages (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, code VARCHAR(5) NOT NULL UNIQUE, name VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE spaces (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL UNIQUE, description TEXT NULL, cover_image VARCHAR(255) NULL, created_by BIGINT UNSIGNED NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, CONSTRAINT fk_spaces_user FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE scenes (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, space_id BIGINT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, panorama_image VARCHAR(255) NOT NULL, initial_pitch FLOAT NOT NULL DEFAULT 0, initial_yaw FLOAT NOT NULL DEFAULT 0, initial_hfov FLOAT NOT NULL DEFAULT 100, order_index INT UNSIGNED NOT NULL DEFAULT 0, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, CONSTRAINT fk_scenes_space FOREIGN KEY(space_id) REFERENCES spaces(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE scene_links (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, from_scene_id BIGINT UNSIGNED NOT NULL, to_scene_id BIGINT UNSIGNED NOT NULL, pitch FLOAT NOT NULL, yaw FLOAT NOT NULL, label VARCHAR(255) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, UNIQUE KEY uq_scene_link(from_scene_id,to_scene_id), CONSTRAINT fk_scene_links_from FOREIGN KEY(from_scene_id) REFERENCES scenes(id) ON DELETE CASCADE, CONSTRAINT fk_scene_links_to FOREIGN KEY(to_scene_id) REFERENCES scenes(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE artifacts (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, description TEXT NULL, period VARCHAR(255) NULL, material VARCHAR(255) NULL, image VARCHAR(255) NULL, video_url VARCHAR(2048) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE hotspots (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, scene_id BIGINT UNSIGNED NOT NULL, artifact_id BIGINT UNSIGNED NULL, pitch FLOAT NOT NULL, yaw FLOAT NOT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, CONSTRAINT fk_hotspots_scene FOREIGN KEY(scene_id) REFERENCES scenes(id) ON DELETE CASCADE, CONSTRAINT fk_hotspots_artifact FOREIGN KEY(artifact_id) REFERENCES artifacts(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE narrations (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, hotspot_id BIGINT UNSIGNED NULL, artifact_id BIGINT UNSIGNED NULL, language_id BIGINT UNSIGNED NOT NULL, audio_path VARCHAR(255) NOT NULL, transcript TEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, CONSTRAINT fk_narrations_hotspot FOREIGN KEY(hotspot_id) REFERENCES hotspots(id) ON DELETE CASCADE, CONSTRAINT fk_narrations_artifact FOREIGN KEY(artifact_id) REFERENCES artifacts(id) ON DELETE CASCADE, CONSTRAINT fk_narrations_language FOREIGN KEY(language_id) REFERENCES languages(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE tours (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, theme VARCHAR(255) NULL, description TEXT NULL, cover_image VARCHAR(255) NULL, created_by BIGINT UNSIGNED NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, CONSTRAINT fk_tours_user FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE tour_steps (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, tour_id BIGINT UNSIGNED NOT NULL, scene_id BIGINT UNSIGNED NOT NULL, order_index INT UNSIGNED NOT NULL DEFAULT 0, note TEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, CONSTRAINT fk_tour_steps_tour FOREIGN KEY(tour_id) REFERENCES tours(id) ON DELETE CASCADE, CONSTRAINT fk_tour_steps_scene FOREIGN KEY(scene_id) REFERENCES scenes(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE guestbook_entries (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NULL, display_name VARCHAR(255) NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, CONSTRAINT fk_guestbook_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE collection_items (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL, artifact_id BIGINT UNSIGNED NOT NULL, note TEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, UNIQUE KEY uq_collection(user_id,artifact_id), CONSTRAINT fk_collection_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE, CONSTRAINT fk_collection_artifact FOREIGN KEY(artifact_id) REFERENCES artifacts(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE visit_logs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NULL, session_id VARCHAR(255) NOT NULL, scene_id BIGINT UNSIGNED NULL, hotspot_id BIGINT UNSIGNED NULL, action ENUM('view_scene','click_hotspot','play_narration','start_tour','finish_tour') NOT NULL, duration_seconds INT UNSIGNED NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, INDEX idx_visit_scene_action(scene_id,action), CONSTRAINT fk_visit_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL, CONSTRAINT fk_visit_scene FOREIGN KEY(scene_id) REFERENCES scenes(id) ON DELETE SET NULL, CONSTRAINT fk_visit_hotspot FOREIGN KEY(hotspot_id) REFERENCES hotspots(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tài khoản ứng dụng riêng; không dùng root.
-- CREATE USER 'baotangso_app'@'localhost' IDENTIFIED BY '<mat-khau-manh>';
-- GRANT ALL PRIVILEGES ON baotang_ao.* TO 'baotangso_app'@'localhost';
-- FLUSH PRIVILEGES;


CREATE TABLE favorites (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 target_type ENUM('artifact','tour') NOT NULL,
 target_id BIGINT UNSIGNED NOT NULL,
 created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
 UNIQUE KEY favorites_user_target_unique(user_id,target_type,target_id),
 INDEX favorites_target_index(target_type,target_id),
 CONSTRAINT fk_favorites_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE feedback (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 target_type ENUM('artifact','tour') NOT NULL,
 target_id BIGINT UNSIGNED NOT NULL,
 rating TINYINT UNSIGNED NOT NULL,
 comment TEXT NOT NULL,
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
 UNIQUE KEY feedback_user_target_unique(user_id,target_type,target_id),
 INDEX feedback_public_lookup_index(target_type,target_id,status),
 INDEX feedback_status_created_index(status,created_at),
 CONSTRAINT fk_feedback_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tour_progress (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 tour_id BIGINT UNSIGNED NOT NULL,
 current_step_order INT UNSIGNED NOT NULL DEFAULT 0,
 visited_scene_ids JSON NULL,
 completed_at TIMESTAMP NULL,
 created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
 UNIQUE KEY tour_progress_user_tour_unique(user_id,tour_id),
 CONSTRAINT fk_tour_progress_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_tour_progress_tour FOREIGN KEY(tour_id) REFERENCES tours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
