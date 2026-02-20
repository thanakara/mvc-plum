create schema if not exists plum;

create type plum.region_type as enum ('EU', 'EUW', 'NA', 'KR');

create index idx_users_active
on plum.users (created_at DESC)
where is_active = true;

create view plum.active_users as
select  usr.email,
        acc.account_name,
        acc.region,
        usr.created_at
from plum.users usr
join plum.accounts acc
    on usr.id = acc.user_id
where usr.is_active = true;
