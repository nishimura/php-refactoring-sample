create table article(
  article_id integer primary key autoincrement,
  title text not null,
  body text not null,
  created_at timestamp not null default current_timestamp,
  updated_at timestamp
);

create table tag(
  article_id integer not null,
  tag text not null,
  primary key(article_id, tag)
);
