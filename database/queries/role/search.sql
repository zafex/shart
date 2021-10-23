select
	t_role.id,
	t_role.identity,
	t_role.label,
	t_role.description,
	t_role.level,
	t_role.status,
	t_role.created_at,
	t_role.created_by,
	count(t_user.id) as users_count,
	count(t_permission.id) as permissions_count
from prefix_sys_role t_role
left join prefix_sys_user_role t_user_role on t_user_role.id_role = t_role.id
left join prefix_sys_user t_user on t_user.id = t_user_role.id_user and t_user.status = 1
left join prefix_sys_role_permission t_role_permission on t_role_permission.id_role = t_role.id
left join prefix_sys_permission t_permission on t_permission.id = t_role_permission.id_permission and t_permission.status = 1
/* where condition */
group by
	t_role.id,
	t_role.identity,
	t_role.label,
	t_role.description,
	t_role.level,
	t_role.status,
	t_role.created_at,
	t_role.created_by