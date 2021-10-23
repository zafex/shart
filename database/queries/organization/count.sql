select
	count(*)
from prefix_mst_organization t_organization
left join prefix_mst_organization t_parent on t_parent.id = t_organization.id_parent
/* where condition */