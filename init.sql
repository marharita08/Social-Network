create table if not exists universities(
 university_id serial primary key,
 name          varchar(500) not null
);

create table if not exists users(
 user_id       serial primary key,
 name          varchar(255) not null,
 role          varchar(10) not null,
 email         varchar(255) not null,
 password      varchar(500) not null,
 phone         char(13),
 university_id int,
 avatar        varchar(255),
 foreign key (university_id) references universities (university_id)
);

create table if not exists status(
 status_id serial primary key,
 status    varchar(25) not null
);

create table if not exists friends(
 request_id   serial primary key,
 from_user_id int not null,
 to_user_id   int not null,
 status_id    int not null,
 unique(from_user_id, to_user_id),
 foreign key (from_user_id) references users (user_id) on delete cascade,
 foreign key (to_user_id) references Users (user_id) on delete cascade,
 foreign key (status_id) references Status (status_id)
);

create table if not exists field_visibilities(
 visibility_id serial primary key,
 visibility    varchar(10) not null
);

create table if not exists user_settings(
 user_id                  int primary key,
 email_visibility_id      int not null default 1,
 phone_visibility_id      int not null default 1,
 university_visibility_id int not null default 1,
 foreign key (user_id) references users (user_id) on delete cascade,
 foreign key (email_visibility_id) references field_visibilities (visibility_id),
 foreign key (phone_visibility_id) references field_visibilities (visibility_id),
 foreign key (university_visibility_id) references field_visibilities (visibility_id)
);

create table if not exists article_visibilities(
 visibility_id serial primary key,
 visibility   varchar(10) not null
);

create table if not exists articles(
 article_id    serial primary key,
 user_id       int not null,
 "text"        text not null,
 visibility_id int not null default 1,
 "created_at"  timestamp not null,
 image         varchar(255),
 foreign key (user_id) references users (user_id) on delete cascade,
 foreign key (visibility_id) references article_visibilities (visibility_id)
);

create table if not exists article_likes(
 article_id int not null,
 user_id    int not null,
 unique(article_id, user_id),
 foreign key (user_id) references users (user_id) on delete cascade,
 foreign key (article_id) references articles (article_id) on delete cascade
);

create table if not exists comments(
 comment_id   serial primary key,
 article_id   int not null,
 user_id      int not null,
 "text"       text not null,
 parent_id    int,
 path         varchar(100),
 level        int not null,
 commented_at timestamp not null,
 foreign key (user_id) references users (user_id) on delete cascade,
 foreign key (article_id) references articles (article_id) on delete cascade,
 foreign key (parent_id) references comments (comment_id) on update cascade on delete cascade
);

insert into status (status)
values ('Under consideration'),
       ('Accepted'),
       ('Denied');

insert into article_visibilities (visibility)
values ('All'),
       ('Friends'),
       ('Only Me');

insert into field_visibilities (visibility)
values ('All'),
       ('Friends'),
       ('Only Me');

insert into universities (name)
values ('Sumy State University'),
       ('Sumy State Pedagogical University'),
       ('Kyiv Polytechnic Institute');

create table if not exists session(
 user_id int not null,
 token   char(37) not null primary key,
 foreign key (user_id) references users (user_id) on delete cascade
);
