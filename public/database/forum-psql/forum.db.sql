
create table if not exists users (
   id serial primary key not null,
   username varchar(200) not null
);

create table if not exists posts (
   id serial primary key not null,
   content text not null,
   user_id integer not null,
   topic_id integer not null,
   created_at TIMESTAMP not null
);

create table if not exists terms (
   id serial primary key not null,
   type_id integer not null,
   title varchar(255) not null,
   created_at TIMESTAMP
);

create table if not exists group_topics (
   id serial primary key not null,
   group_id integer not null,
   topic_id integer not null
);

create table if not exists term_types (
   id serial primary key not null,
   label varchar(100) unique
);

ALTER TABLE group_topics
   ADD CONSTRAINT fk_group_topics_group_id
   FOREIGN KEY (group_id)
   REFERENCES terms(id) ON DELETE CASCADE;

ALTER TABLE group_topics
   ADD CONSTRAINT fk_group_topics_topic_id
   FOREIGN KEY (topic_id)
   REFERENCES terms(id) ON DELETE CASCADE;

ALTER TABLE posts
   ADD CONSTRAINT fk_posts_user_id
   FOREIGN KEY (user_id)
   REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE posts
   ADD CONSTRAINT fk_posts_topic_id
   FOREIGN KEY (topic_id)
   REFERENCES terms(id) ON DELETE CASCADE;

ALTER TABLE terms
   ADD CONSTRAINT fk_terms_type_id
   FOREIGN KEY (type_id)
   REFERENCES term_types(id) ON DELETE CASCADE;


insert into term_types (label) values('Groups'), ('Topics'), ('Tags')
  on conflict (label) do update set label = EXCLUDED.label;
