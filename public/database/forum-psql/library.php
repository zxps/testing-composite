<?php

static $db;

function db (): PDO {
    static $db;
    if ($db) {
        return $db;
    }
    $db = new PDO('pgsql:host=db;port=5432;dbname=blogger;user=blogger;password=blogger');
    return $db;
}

function db_query($query, array $params = []) {
    $db = db();
    if ($params) {
        $stmt = $db->prepare($query);
        foreach($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        if (!$stmt) {
            print_r($db->errorInfo());
            exit;
        }
        $stmt->execute();
        return $stmt;
    } else {
        return $db->query($query);
    }
}

function db_del(string $table, $id) {
    if (!$id) {
        return null;
    }
    return db_query('delete from ' . $table . ' where id = :id', [
        ':id' => $id
    ]);
}

function db_exec($query) {
    return db()->exec($query);
}

function get_username ($userId) {
    return db_query('
        select u.username from users u where id = :id
    ', [':id' => $userId])->fetchColumn();
}

function get_date($date, $format = 'd.m.y H:i') {
    $time = strtotime($date);
    return date($format, $time);
}

function get_trends() {
    $stmt = db_query('
        select
               p.topic_id,
               p.topic,
               p.created_at,
               p.content,
               p.user
        from (
               select
                 p.topic_id,
                 (select t.title
                   from terms t
                     where t.id = p.topic_id) as topic,
                 (select max(p2.created_at)
                   from posts p2
                     where p2.topic_id = p.topic_id) as created_at,
                 (select p3.content
                   from posts p3
                     where p3.topic_id = p.topic_id
                     order by p3.created_at desc
                   limit 1) as content,
                 (select u.username
                   from users u
                   join posts p4 on (u.id = p4.user_id)
                     where p4.topic_id = p.topic_id
                   order by p4.created_at desc
                   limit 1
                   ) as user
               from posts p
               group by p.topic_id
               having count(distinct p.user_id) > 3
             ) p
             order by p.created_at desc
        limit 10
    ');
    if (!$stmt) {
        print_r(db()->errorInfo());
        exit;
    }
    return $stmt->fetchAll();
}

function get_topic_posts($topicId) {
    return db_query('
        select p.* from posts p
            where p.topic_id = :topic_id
        order by p.created_at desc
    ', [':topic_id' => $topicId])->fetchAll();
}

function get_group_posts_count($groupId) {
    return db_query('
        select count(*) from posts p
            join group_topics gt on (gt.topic_id = p.topic_id)
        where gt.group_id = :group_id
    ', [':group_id' => $groupId])->fetchColumn();
}

function get_topic_post_count($topicId) {
    return db_query('
        select count(*) from posts p
            where p.topic_id = :topic_id
    ', [':topic_id' => $topicId])->fetchColumn();
}

function get_topics($groupId) {
    return db_query('
        select t.* from terms t
            join group_topics gt on t.id = gt.topic_id
        where t.type_id = :type_id 
              and gt.group_id = :group_id
        group by t.id           
    ', [
        ':type_id' => get_term_type_id('Topics'),
        ':group_id' => $groupId
    ])->fetchAll();
}

function get_term_type_id($label) {
    $stmt = db()->prepare('select t.id from term_types t where t.label = :label');
    $stmt->bindValue('label', $label);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function get($key, $default = null) {
    if (isset($_GET[$key]) && $_GET[$key]) {
        return trim($_GET[$key]);
    }
    if (isset($_POST[$key]) && $_POST[$key]) {
        return trim($_POST[$key]);
    }

    return $default;
}

function act() {
    return get('act');
}

function getTermTypes() {
    $db = db();
    $query = $db->query('Select t.* from term_types t');
    if ($query) {
        $types = $query->fetchAll();
        return array_map(function ($t) {
            return $t['label'];
        }, $types);
    }
    return [];
}