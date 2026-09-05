# Database Schema Documentation

### Table: `drivers`
| Column | Data Type | Modifiers/Keys |
| :--- | :--- | :--- |
| `id` | bigint unsigned | NOT NULL, AUTO_INCREMENT, PRIMARY KEY |
| `name` | varchar(255) | NOT NULL |
| `team` | varchar(255) | NOT NULL |
| `number` | int | NOT NULL |
| `year` | int | NOT NULL |
| `created_at` | timestamp | NULL, DEFAULT NULL |
| `updated_at` | timestamp | NULL, DEFAULT NULL |

---

### Table: `picks`
| Column | Data Type | Modifiers/Keys |
| :--- | :--- | :--- |
| `id` | bigint unsigned | NOT NULL, AUTO_INCREMENT, PRIMARY KEY |
| `user_id` | bigint unsigned | NOT NULL, FOREIGN KEY (users.id) |
| `score` | float | NOT NULL |
| `d1_id` | bigint unsigned | DEFAULT NULL, FOREIGN KEY (drivers.id) |
| `d2_id` | bigint unsigned | DEFAULT NULL, FOREIGN KEY (drivers.id) |
| `d3_id` | bigint unsigned | DEFAULT NULL, FOREIGN KEY (drivers.id) |
| `bonus` | double | NOT NULL |
| `session_key` | int | NOT NULL |
| `created_at` | timestamp | NULL, DEFAULT NULL |
| `updated_at` | timestamp | NULL, DEFAULT NULL |

---

### Table: `races`
| Column | Data Type | Modifiers/Keys |
| :--- | :--- | :--- |
| `id` | bigint unsigned | NOT NULL, AUTO_INCREMENT, PRIMARY KEY |
| `session_key` | int | NOT NULL |
| `date_start` | timestamp | NOT NULL |
| `name` | varchar(255) | NOT NULL |
| `type` | varchar(255) | NOT NULL |
| `created_at` | timestamp | NULL, DEFAULT NULL |
| `updated_at` | timestamp | NULL, DEFAULT NULL |

---

### Table: `users`
| Column | Data Type | Modifiers/Keys |
| :--- | :--- | :--- |
| `id` | bigint unsigned | NOT NULL, AUTO_INCREMENT, PRIMARY KEY |
| `name` | varchar(255) | NOT NULL |
| `email` | varchar(255) | NOT NULL, UNIQUE KEY |
| `email_verified_at` | timestamp | NULL, DEFAULT NULL |
| `password` | varchar(255) | NOT NULL |
| `remember_token` | varchar(100) | DEFAULT NULL |
| `created_at` | timestamp | NULL, DEFAULT NULL |
| `updated_at` | timestamp | NULL, DEFAULT NULL |

---

### Table: `winners`
| Column | Data Type | Modifiers/Keys |
| :--- | :--- | :--- |
| `id` | bigint unsigned | NOT NULL, AUTO_INCREMENT, PRIMARY KEY |
| `driver_id` | bigint unsigned | DEFAULT NULL, FOREIGN KEY (drivers.id) |
| `position` | int | NOT NULL |
| `session_key` | int | NOT NULL |
| `created_at` | timestamp | NULL, DEFAULT NULL |
| `updated_at` | timestamp | NULL, DEFAULT NULL |