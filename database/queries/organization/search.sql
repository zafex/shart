select
	t_organization.id,
	t_organization.id_parent,
	t_organization.name,
	t_organization.description,
	t_organization.code,
	t_organization.level,
	t_parent.name as organization,
	t_organization.created_at,
	t_organization.created_by
from prefix_mst_organization t_organization
left join prefix_mst_organization t_parent on t_parent.id = t_organization.id_parent
/* where condition */