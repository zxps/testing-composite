<?php
require_once __DIR__ . '/library.php';
require_once __DIR__ . '/controller.php';
?>
<html lang="en">
<head>
    <title>Simple Single Page PostgreSQL Forum</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php include __DIR__ . '/../../header.php' ?>
<div style="font-size: 1.3em;">
<a href="?act=create_database">create database</a> |
<a href="?act=drop_database">drop database</a>
</div>
<br/>
<div style="width: 100%; display: flex;">
    <div style="float:left; width: 30%; border: 1px solid black; padding: 1em;">
        <form action="">
            <input type="hidden" name="act" value="create_group"/>
            <h4>Create Group:</h4>
            <label>
                Group name:<br/>
                <input type="text" name="name" placeholder="Group name"/>
            </label>
            <br/>
            <input type="submit" value="Create"/>
        </form>
        <hr/>
        <form action="?act=create_topic">
            <input type="hidden" name="act" value="create_topic"/>
            <h4>Create Topic:</h4>
            <label>
                Group
                <select name="group_id">
                    <?php $groups = db_query(
                        'select t.* from terms t where t.type_id = :type_id order by t.title ASC',
                        ['type_id' => get_term_type_id('Groups')])->fetchAll(); ?>
                    <?php foreach($groups as $group): ?>
                    <option value="<?php echo $group['id'] ?>"><?php echo $group['title'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
            <br/>
            <label>
                Topic name:
                <input type="text" name="name" placeholder="Topic name"/>
            </label>
            <br/>
            <input type="submit" value="Create"/>
        </form>
        <hr/>
        <form action="">
            <input type="hidden" name="act" value="create_user"/>
            <h4>Create user</h4>
            <label>
                User name:
                <input type="text" name="name" placeholder="User name"/>
            </label>
            <br/>
            <input type="submit" value="Create"/>
        </form>
    </div>
    <div style="float:right; width: 70%; border: 1px solid black; border-left: 0px; padding: 1em;">
        <div style="" id="users">
            <p>Users:</p>
            <?php $users = db()->query('Select u.* from users u')->fetchAll() ?>
            <ul>
                <?php foreach($users as $user): ?>
                    <li user-id="<?php echo $user['id'] ?>" username="<?php echo $user['username'] ?>">
                        <a href=""><?php echo $user['username'] ?> (id:<?php echo $user['id'] ?>)</a>
                        <a href="?act=delete_user&id=<?php echo $user['id'] ?>">[delete]</a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="top-trending">
            <p>Top trending</p>
            <?php $trends = get_trends() ?>
            <ul>
            <?php foreach($trends as $trend) : ?>
                <li>
                    <span class="topic"><?php echo $trend['topic'] ?></span>
                    <span class="content">
                        <span class="user"><b><?php echo $trend['user'] ?></b> says: </span>
                        <?php echo $trend['content'] ?>
                        <span class="created-at"> at <b><?php echo get_date($trend['created_at'], 'H:i d.m.Y') ?></b></span>
                    </span>
                </li>
            <?php endforeach ?>
            </ul>
        </div>
        <hr/>
        <div style="">
            <p>Groups</p>
            <?php $groups = db_query('select t.* from terms t where t.type_id = :type_id order by t.title asc', [':type_id' => (int) get_term_type_id('Groups')])->fetchAll(); ?>
            <?php foreach($groups as $group): ?>
            <li style="padding-bottom: 0.5em;" id="group_<?php echo $group['id'] ?>">
                <a class="group" href=""><?php echo $group['title'] ?> (<?php echo get_group_posts_count($group['id']) ?>)</a>
                <a href="?act=delete_term&id=<?php echo $group['id'] ?>">[delete]</a>
                <ul style="padding-left: 1em; padding-top: 0.3em;">
                    <?php $topics = get_topics($group['id']) ?>
                    <?php foreach($topics as $topic): ?>
                        <li class="topic" id="topic_<?php echo $topic['id'] ?>">
                            <a href=""><?php echo $topic['title'] ?> (<?php echo get_topic_post_count($topic['id']) ?>)</a>
                            <a href="?act=delete_term&id=<?php echo $topic['id'] ?>">[delete]</a>
                            <a href="javascript:void(0)" onclick="createPost(this, <?php echo $topic['id'] ?>)">[create post]</a>
                            <?php $posts = get_topic_posts($topic['id']) ?>
                            <ul class="posts">
                            <?php foreach($posts as $post): ?>
                                <li id="post_<?php echo $post['id'] ?>">
                                    <span class="content"><?php echo $post['content'] ?></span>
                                    <br/>
                                    <span class="user">by <b><?php echo get_username($post['user_id']) ?></b>, </span>
                                    <span class="date">at <?php echo get_date($post['created_at']) ?></span>
                                </li>
                            <?php endforeach ?>
                            </ul>
                        </li>
                    <?php endforeach ?>
                </ul>
            </li>
            <?php endforeach ?>
        </div>
    </div>
</div>
</body>
<script type="text/javascript" src="main.js"></script>
</html>