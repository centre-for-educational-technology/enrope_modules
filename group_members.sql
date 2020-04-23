select f.field_first_name_value as First_name, l.field_last_name_value as Last_name, u.mail as Mail
from users_field_data as u
join group_content_field_data as g on g.entity_id = u.uid
join user__field_first_name as f on f.entity_id = u.uid
join user__field_last_name as l on l.entity_id = u.uid
where g.gid = 12;
