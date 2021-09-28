ALTER TABLE `task` ADD `last_comment_time` datetime(0) NULL;
ALTER TABLE `task` ADD `comment_count` INT(11) NULL;
ALTER TABLE `task` ADD `creator_unseen_comment_count` INT(11) NULL;
ALTER TABLE `task` ADD `assignee_unseen_comment_count` INT(11) NULL;

UPDATE task
SET comment_count = ( SELECT count(*) from projectcomment WHERE task_id = task.id ),
last_comment_time = ( SELECT MAX(updated_at) from projectcomment WHERE task_id = task.id );