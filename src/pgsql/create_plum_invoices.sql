
create table plum.invoices (
    id integer generated always as identity primary key,
    user_id integer,
    amount decimal(10, 4) not null,
    
    constraint fk_user
        foreign key (user_id) 
        references cu.users(id)
        on delete set null on update cascade
);
