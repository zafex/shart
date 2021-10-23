select
	count(distinct t_user.id)
from prefix_sys_user t_user
left join prefix_sys_user_credential t_credential on t_credential.id_user = t_user.id
left join prefix_sys_user_role t_user_role on t_user_role.id_user = t_user.id
left join prefix_sys_role t_role on t_role.id = t_user_role.id_role
/* where condition */