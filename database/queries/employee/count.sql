select
	count(*)
from prefix_mst_employee t_employee
join prefix_mst_organization t_organization on t_organization.id = t_employee.id_organization
/* where condition */