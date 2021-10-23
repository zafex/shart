select
	t_position.id,
	t_position.id_organization,
	t_position.id_type,
	t_position.name,
	t_position.description,
	t_position.code,
	t_setting_detail.label as type,
	t_organization.name as organization,
	t_position.created_at,
	t_position.created_by
from prefix_mst_position t_position
join prefix_sys_setting_detail t_setting_detail on t_setting_detail.id = t_position.id_type
left join prefix_mst_organization t_organization on t_organization.id = t_position.id_organization
/* where condition */