create table plum.users (
    id integer generated always as identity primary key,
    email varchar(100) not null,
    is_active boolean not null default false,
    created_at timestamp not null default current_timestamp
);

create table plum.accounts (
    id integer generated always as identity primary key,
    user_id integer,
    account_name varchar(100) not null,
    region plum.region_type not null,
    
    constraint fk_user
        foreign key (user_id) 
        references plum.users(id)
        on delete set null on update cascade
);
