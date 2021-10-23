select
	t_user.id,
	t_user.username,
	t_user.fullname,
	t_user.email,
	count(t_role.id) as roles_count,
	count(t_credential.id) as credentials_count,
	t_user.created_at,
	t_user.created_by,
	t_user.updated_at,
	t_user.updated_by
from prefix_sys_user t_user
left join prefix_sys_user_credential t_credential on t_credential.id_user = t_user.id
left join prefix_sys_user_role t_user_role on t_user_role.id_user = t_user.id
left join prefix_sys_role t_role on t_role.id = t_user_role.id_role
/* where condition */
group by
	t_user.id,
	t_user.username,
	t_user.fullname,
	t_user.email,
	t_user.created_at,
	t_user.created_by,
	t_user.updated_at,
	t_user.updated_by