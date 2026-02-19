create table plum.users (
    id integer generated always as identity primary key,
    fullname varchar(100) not null,
    email varchar(100) not null unique,
    is_active boolean not null default false,
    created_at timestamp not null default current_timestamp
);
