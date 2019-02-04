<?php
require_once __DIR__ . '/library.php';

$db = db();
$act = act();

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

if ('create_database' == $act) {
    $db->exec(file_get_contents(__DIR__ . '/forum.db.sql'));
    header('Location: /database/forum-psql/index', true);
    exit();
} else if ('drop_database' == $act) {
    $tables = ['users', 'terms', 'posts', 'term_types', 'term_assoc', 'group_topics'];
    foreach($tables as $table) {
        $db->exec('drop table if exists ' . $table);
    }
    header('Location: /database/forum-psql/index', true);
    exit();
} else if ('create_user' == $act) {
    $name = get('name');
    if ($name) {
        $stmt = $db->prepare('insert into users(username) values(:username)');
        $stmt->bindValue(':username', $name);
        $stmt->execute();
    }
    header('Location: /database/forum-psql/index', true);
    exit();
} else if ('delete_user' === $act) {
    db_del('users', get('id'));
    header('Location: /database/forum-psql/index', true);

} else if ('create_group' === $act) {
    $name = get('name');
    if ($name) {
        db_query('insert into terms (type_id, title, created_at) values(:type_id, :title, current_timestamp)', [
            ':type_id' => (int)get_term_type_id('Groups'),
            ':title' => $name
        ]);
    }
    header('Location: /database/forum-psql/index', true);
} else if ('create_topic' === $act) {
    $groupId = get('group_id');
    $name = get('name');
    if ($groupId && $name) {
        db_query('
            insert into terms (type_id, title, created_at) 
                values(:type_id, :title, current_timestamp)', [
            ':type_id' => get_term_type_id('Topics'),
            ':title' => $name
        ]);
        $topicId = $db->lastInsertId();

        db_query('
           insert into group_topics(group_id, topic_id)
               values(:group_id, :topic_id) 
           ', [':group_id' => $groupId, ':topic_id' => (int)$topicId]);
    }
    header('Location: /database/forum-psql/index', true);
} else if ('create_post' === $act) {
    $userId = get('user_id');
    $content = get('content');
    $topicId = get('topic_id');
    if ($userId && $content && $topicId) {
        db_query('
            insert into posts (user_id, topic_id, content, created_at) 
                values (:user_id, :topic_id, :content, current_timestamp)', [
            ':user_id' => (int) $userId,
            ':topic_id' => (int) $topicId,
            ':content' => $content
        ]);
    }

    header('Location: /database/forum-psql/index', true);

} else if ('delete_term' === $act) {
    db_del('terms', (int) get('id'));
    header('Location: /database/forum-psql/index', true);
}
