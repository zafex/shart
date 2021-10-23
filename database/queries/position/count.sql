select
	count(distinct t_position.id)
from prefix_mst_position t_position
join prefix_sys_setting_detail t_setting_detail on t_setting_detail.id = t_position.id_type
left join prefix_mst_organization t_organization on t_organization.id = t_position.id_organization
/* where condition */