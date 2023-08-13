<?php


namespace app\components;


use telegram\TelegramAlert;
use Yii;
use yii\db\Exception;

class MySqlBase
{
    public yii\db\Connection $db;
    protected string $host;
    protected string $dbname;
    protected string $user;
    protected string $password;

    function __construct(string $host,string $dbname,string $user,string $password)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->password = $password;
    }

    protected function ConnectionBase():void{
        $this->db = new yii\db\Connection([
            'dsn' => 'mysql:host='.$this->host.';dbname='.$this->dbname,
            'username' => $this->user,
            'password' => $this->password,
            'charset' => 'utf8',
        ]);
    }

    public function CreatDataBase():void
    {
        $sql = "CREATE DATABASE IF NOT EXISTS $this->dbname
        CHARACTER SET utf8
        COLLATE utf8_general_ci;
        CREATE USER '$this->user'@'%' IDENTIFIED BY '$this->password';
        FLUSH PRIVILEGES;
        GRANT ALL PRIVILEGES ON $this->dbname.* TO '$this->user'@'%';
        FLUSH PRIVILEGES;
        ";
        try {
            \Yii::$app->db->createCommand($sql)->execute();
            \Yii::$app->getSession()->setFlash('success', 'База добавлена');
        }
        catch ( \Exception $e ) {
            \Yii::$app->getSession()->setFlash('error', 'Ошибка при добавлении базы: '. $e->getMessage());
            return;
        }

    }

    public function DeleteDataBase():void
    {
        $sql = "DROP DATABASE IF EXISTS $this->dbname;
        REVOKE ALL PRIVILEGES, GRANT OPTION FROM '$this->user'@'%';
        DROP USER '$this->user'@'%';
        ";
        try {
            \Yii::$app->db->createCommand($sql)->execute();
            \Yii::$app->getSession()->setFlash('success', 'База удалена');
        }
        catch ( \Exception $e ) {
            \Yii::$app->getSession()->setFlash('error', 'Ошибка при удалении базы: '. $e->getMessage());
            return;
        }
    }



    public function InstallTableAmo():void{
        $this->ConnectionBase();
        $sql_requests = $this->RequestsBaseAmo();
        foreach($sql_requests as $query){
            $this->db->createCommand($query)->execute();
        }
    }


    private function RequestsBaseAmo():array{
        $sqls = [];

        $sqlsp[] = "DROP TABLE IF EXISTS `group`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `status_history`;
DROP TABLE IF EXISTS `pipeline`;
DROP TABLE IF EXISTS `source`;
DROP TABLE IF EXISTS `status`;
DROP TABLE IF EXISTS `contact`;
DROP TABLE IF EXISTS `company`;
DROP TABLE IF EXISTS `custom_fields`;
DROP TABLE IF EXISTS `token_crm`;
DROP TABLE IF EXISTS `temp_key`;
DROP TABLE IF EXISTS `account`;
DROP TABLE IF EXISTS `task`;
DROP TABLE IF EXISTS `call`;";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `group` (
        `id` INT(11) UNSIGNED NOT NULL,
        `name` TEXT NULL DEFAULT NULL,
        PRIMARY KEY (`id`))
        ENGINE = InnoDB
        DEFAULT CHARACTER SET = utf8mb4
        COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `user` (
        `id` INT(11) UNSIGNED NOT NULL,
        `name` TEXT NULL DEFAULT NULL,
        `group_id` INT(11) NULL DEFAULT NULL,
        PRIMARY KEY (`id`))
        ENGINE = InnoDB
        DEFAULT CHARACTER SET = utf8mb4
        COLLATE = utf8mb4_unicode_ci";


        $sqls[] = "CREATE TABLE IF NOT EXISTS `status_history` (
        `id` INT(11) UNSIGNED NOT NULL,
  `responsible_user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `created_user_id` INT(11) NULL DEFAULT NULL,
  `created_date` DATETIME NULL DEFAULT NULL,
  `updated_date` DATETIME NULL DEFAULT NULL,
  `account_id` INT(11) NULL DEFAULT NULL,
  `status_new` INT(11) NULL DEFAULT NULL,
  `status_old` INT(11) NULL DEFAULT NULL,
  `pipeline_id_old` INT(11) NULL DEFAULT NULL,
  `pipeline_id_new` INT(11) NULL DEFAULT NULL,
  `lead_id` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";


        $sqls[] = "CREATE TABLE IF NOT EXISTS `pipeline` (
        `id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(127) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `source` (
        `id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(127) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `status` (
        `id` INT(11) UNSIGNED NOT NULL,
        `pipeline_id` INT(11) UNSIGNED NOT NULL,
        `name` VARCHAR(127) NULL DEFAULT NULL,
        PRIMARY KEY (`id`,`pipeline_id`))
        ENGINE = InnoDB
        DEFAULT CHARACTER SET = utf8mb4
        COLLATE = utf8mb4_unicode_ci";


        $sqls[] = "CREATE TABLE IF NOT EXISTS `lead` (
        `id` INT(11) UNSIGNED NOT NULL,
  `name` TEXT NULL DEFAULT NULL,
  `status_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `sale` DECIMAL(11,2) NULL DEFAULT NULL,
  `responsible_user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `updated_by` INT(11) UNSIGNED NULL DEFAULT NULL,
  `created_by` INT(11) UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `pipeline_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `account_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `contact_id` INT(11) UNSIGNED NULL DEFAULT NULL,
    `company_id` INT(11) UNSIGNED NULL DEFAULT NULL, 
    `group_id` INT(11) NULL DEFAULT NULL,
    `loss_reason_id` INT(11) UNSIGNED NULL DEFAULT NULL,
    `closed_at` DATETIME NULL DEFAULT NULL,
    `score` INT(11) NULL DEFAULT NULL,
    `tags` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `contact` (
        `id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `responsible_user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `updated_by` INT(11) UNSIGNED NULL DEFAULT NULL,
  `created_by` INT(11) UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `account_id` INT(11) UNSIGNED NULL DEFAULT NULL,
    `group_id` INT(11)  NULL DEFAULT NULL,
    `score` INT(11) NULL DEFAULT NULL,
    `tags` TEXT NULL DEFAULT NULL,
    `company_id` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `company` (
        `id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `responsible_user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `updated_by` INT(11) UNSIGNED NULL DEFAULT NULL,
  `created_by` INT(11) UNSIGNED NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `account_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `contact_id` INT(11) UNSIGNED NULL DEFAULT NULL,
    `group_id` INT(11) NULL DEFAULT NULL,
    `tags` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `custom_fields` (
        `id` INT(11) UNSIGNED NOT NULL,
    `name` TEXT NULL DEFAULT NULL,
    `code` TEXT NULL DEFAULT NULL,
    `translit` TEXT NULL DEFAULT NULL,
    `type` INT(3) NULL DEFAULT NULL,
    `entity_type` INT(1) NULL DEFAULT NULL,
    `active` INT(1) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `token_crm` (
        `client_id` VARCHAR(255) NOT NULL,
    `client_secret` TEXT NULL DEFAULT NULL,
    `access_token` TEXT NULL DEFAULT NULL,
    `refresh_token` TEXT NULL DEFAULT NULL,
    `expires_in` INT(10) NULL DEFAULT NULL,
    `date_update` DATETIME NULL DEFAULT NULL,
    `subdomain` VARCHAR(230) NOT NULL,
    `error_log` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`subdomain`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `temp_key` (
        `client_id` VARCHAR(255) NOT NULL,
    `client_secret` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`client_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `account` (
        `id` INT(11) UNSIGNED NOT NULL,
    `timezone` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `task_type` (
        `id` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci";

        $sqls[] = "CREATE TABLE IF NOT EXISTS task (
  id int(10) UNSIGNED NOT NULL,
  element_id int(10) UNSIGNED DEFAULT NULL,
  element_type int(11) DEFAULT NULL,
  task_type int(11) DEFAULT NULL,
  created_at datetime DEFAULT NULL,
  text text DEFAULT NULL,
  is_completed int(10) UNSIGNED DEFAULT NULL,
  account_id int(10) UNSIGNED DEFAULT NULL,
  created_by int(10) UNSIGNED DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  responsible_user_id int(11) UNSIGNED DEFAULT NULL,
  complete_till_at datetime DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_unicode_ci;

ALTER TABLE task
ADD INDEX lead_id (lead_id);

ALTER TABLE task
ADD INDEX `create` (created_at);

ALTER TABLE task
ADD INDEX `update` (updated_at);

ALTER TABLE task
ADD INDEX complete (complete_till_at);

ALTER TABLE task
ADD INDEX status (is_completed);";

        $sqls[] = "CREATE TABLE IF NOT EXISTS `call` (
  element_id int(11) DEFAULT NULL,
  element_type int(11) DEFAULT NULL,
  note_type int(11) DEFAULT NULL,
  unique_id varchar(127) DEFAULT NULL,
  record_link text DEFAULT NULL,
  phone varchar(255) DEFAULT NULL,
  duration decimal(10, 2) DEFAULT NULL,
  source varchar(127) DEFAULT NULL,
  call_status int(11) DEFAULT NULL,
  created_user_id int(11) DEFAULT NULL,
  text text DEFAULT NULL,
  account_id int(11) DEFAULT NULL,
  created_at datetime DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  responsible_user_id int(11) DEFAULT NULL,
  id int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_unicode_ci;

ALTER TABLE `call`
ADD INDEX note_element (element_id);

ALTER TABLE `call`
ADD INDEX note_type (note_type);

ALTER TABLE `call`
ADD INDEX param_unic (unique_id);

ALTER TABLE `call`
ADD INDEX param_src (source);

ALTER TABLE `call`
ADD INDEX created (created_at);";

$sqls[] = "CREATE OR REPLACE
VIEW view_leads
AS
	SELECT
	   `lead`.`id` AS `id`,
	  `user`.`name` AS `name_user`,
	  `pipeline`.`name` AS `name_pipeline`,
	  `status`.`name` AS `name_status`,
	  `lead`.`name` AS `lead_name`,
	  `contact`.`name` AS `contact_name`,
	  `company`.`name` AS `company_name`,
	  `lead`.`sale` AS `price`,
	  `lead`.`created_by` AS `created_by`,
	  `lead`.`updated_by` AS `updated_by`,
	  `lead`.`closed_at` AS `closed_at`,
	  `lead`.`created_at` AS `created_at`,
	  `lead`.`updated_at` AS `updated_at`,
	  `lead`.`account_id` AS `account_id`,
      `lead`.`contact_id` AS `contact_id`,
      `company`.`id` AS `company_id`
    FROM (((((`lead`
      LEFT JOIN `user`
        ON ((`lead`.`responsible_user_id` = `user`.`id`)))
      LEFT JOIN `pipeline`
        ON ((`lead`.`pipeline_id` = `pipeline`.`id`)))
      LEFT JOIN `contact`
        ON ((`lead`.`contact_id` = `contact`.`id`)))
      LEFT JOIN `status`
        ON (((`lead`.`pipeline_id` = `status`.`pipeline_id`)
        AND (`lead`.`status_id` = `status`.`id`))))
      LEFT JOIN `company`
        ON ((`lead`.`company_id` = `company`.`id`)))";


        $sqls[] = "CREATE OR REPLACE
VIEW view_status_history
AS
SELECT
  status_history.id AS id,
  user.name AS user_name,
  `group`.name AS group_name,
  status_history.created_date AS created_date,
  status_history.account_id AS account_id,
  status_history.lead_id AS lead_id,
  t_st_new.name AS status_new,
  t_st_old.name AS status_old,
  t_p_new.name AS pipeline_new_name,
  t_p_old.name AS pipeline_old_name
FROM status_history
  LEFT OUTER JOIN user
    ON status_history.created_user_id = user.id
  LEFT OUTER JOIN `group`
    ON user.group_id = `group`.id
  LEFT OUTER JOIN status t_st_new
    ON status_history.status_new = t_st_new.id
    AND status_history.pipeline_id_new = t_st_new.pipeline_id
  LEFT OUTER JOIN status t_st_old
    ON status_history.status_old = t_st_old.id
    AND status_history.pipeline_id_old = t_st_old.pipeline_id
  LEFT OUTER JOIN pipeline t_p_new
    ON status_history.pipeline_id_new = t_p_new.id
  LEFT OUTER JOIN pipeline t_p_old
    ON status_history.pipeline_id_old = t_p_old.id";

        $sqls[] = "CREATE OR REPLACE
VIEW view_task
AS
SELECT
  user.name AS user_create,
  user_1.name AS user_responsible,
  task.id AS id,
  task.element_id AS element_id,
  task.element_type AS element_type,
  task.task_type AS task_type,
  task.created_at AS created_at,
  task.text AS text,
  task.is_completed AS is_completed,
  task.account_id AS account_id,
  task.created_by AS created_by,
  task.updated_at AS updated_at,
  task.responsible_user_id AS responsible_user_id,
  task.complete_till_at AS complete_till_at,
  task_type.name AS task_type_name
FROM task
  LEFT OUTER JOIN user
    ON task.created_by = user.id
  LEFT OUTER JOIN user user_1
    ON task.responsible_user_id = user_1.id
  LEFT OUTER JOIN task_type
    ON task.task_type = task_type.id";

        $sqls[] = "CREATE OR REPLACE
VIEW view_call
AS
SELECT
  `user`.`name` AS `user_create`,
  `user_1`.`name` AS `user_responsible`,
  `call`.`element_id` AS `element_id`,
  `call`.`element_type` AS `element_type`,
  `call`.`note_type` AS `note_type`,
  `call`.`unique_id` AS `unique_id`,
  `call`.`record_link` AS `record_link`,
  `call`.`phone` AS `phone`,
  `call`.`duration` AS `duration`,
  `call`.`source` AS `source`,
  `call`.`call_status` AS `call_status`,
  `call`.`text` AS `text`,
  `call`.`account_id` AS `account_id`,
  `call`.`created_at` AS `created_at`,
  `call`.`updated_at` AS `updated_at`,
  `call`.`id` AS `id`
FROM ((`call`
  LEFT JOIN `user`
    ON ((`call`.`created_user_id` = `user`.`id`)))
  LEFT JOIN `user` `user_1`
    ON ((`call`.`responsible_user_id` = `user_1`.`id`)))";

        return $sqls;
    }



}
